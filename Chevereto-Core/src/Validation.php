<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Exception;
use stdClass;

/**
 * # Chevereto\Core\Validation
 * Performs one-line and multiple validation with support for re-usable validators.
 *
 * ## Single validation
 * One-liner validation, it will throw an `Exception` if the input value doesn't validate.
 *
 * ```
 * $validation = Validation::single(id, value, callable, message=null); // (bool)
 * ```
 *
 * ## Multiple validation
 * Validate multiple inputs provided by the `add` method.
 * The `validate` method will return TRUE only if everything validates.
 * Throws exception on if one or more input doesn't validate
 * ```
 * $validation = new Validation();
 * $validation->add(id1, value1, callable1, message1=null);
 * $validation->add(id1, value1, callable1, message1=null);
 * $validation->validate(); // bool
 * ```
 *
 * ## Grouped validation (same input, different callables and messages)
 * Note: you can mix it with ::add().
 * ```
 * $validation = Validation::group(id, value);
 * $validation->append(subid1, callable1, message1=null);
 * $validation->append(subid2, callable1, message1=null);
 * $validation->validate(); // bool
 * ```
 *
 * ## Result set
 * ```
 * $validation->valid(); // Valid results
 * $validation->invalid(); // Invalid results
 * $validation->results(); // The whole thing
 * $validation->message(); // Message global
 * ```
 *
 * ## Valid **callable** types
 * - Closures
 * - Local class static method callable (::method)
 * - Namespaced class static method callback (Class::method)
 *
 * Note: Callables must return bool.
 *
 * ## Remarks
 * message is *always* optional.
 */
class Validation
{
    // Validation tasks
    protected $tasks = [];
    // Valid results (ids)
    protected $valid = [];
    // Invalid results (ids)
    protected $invalid = [];
    // True ig the whole tasks validates
    protected $validates; // bool
    // Output message (for all tasks)
    protected $message;
    // Results
    protected $results;
    // Number of tasks
    protected $taskCount = 0;
    // Re-use property, for the re-usable validator (same callable & message, different values)
    protected $reuse;
    // Group property, for the grouped validator (same value, different callables and messages)
    protected $group;
    // Flag used for single mode
    protected $isSingle;

    /**
     * Single validation shorthand.
     *
     * Performs single-line validation for a given value.
     *
     * @param string $id       validation id
     * @param mixed  $value    value to test against for
     * @param mixed  $callable A callable (string, closure, ::method and Class:method)
     * @param string $message  message for Exception
     *
     * @return bool true if validation succed
     */
    public static function single(string $id, $value, $callable, string $message = null): bool
    {
        $args = func_get_args();
        array_unshift($args, 'validation');

        return (new static())
            ->add(...func_get_args())
            ->validate();
    }

    /**
     * Grouped validation shorthand (aka the grupillo stuff).
     *
     * Used to perform grouped validation (same value, different callables and messages).
     *
     * @param string $id
     * @param mixed  $value
     */
    public static function grouped(string $id, $value): self
    {
        return (new static())
            ->group(...func_get_args());
    }

    public function group(string $id, $value): self
    {
        // TODO: Validate $id
        $this->group = func_get_args();

        return $this;
    }

    /**
     * Adds a new validation task.
     *
     * @param string $id       the input identifier
     * @param mixed  $value    value to test agains for
     * @param mixed  $callable Callable (callable, string)
     * @param string $message  Message returned if the input doesn't validate. Use %v for input value, %s for htmlspecialchars value and %i for id.
     *
     * @return $this
     */
    public function add(string $id, $value, $callable, string $message = null): self
    {
        // Validate ID
        if (strlen($id) == 0 || ctype_space($id)) {
            throw new Exception(
                (new Message("Argument %a can't be empty neither contain only spaces."))
                    ->code('%a', '$id')
            );
        }
        // Validate unique ID
        if (isset($this->tasks[$id])) {
            throw new Exception(
                (new Message('Validation task %t has been already declared. Must use a different task %a.'))
                    ->code('%t', $id)
                    ->code('%a', '$id')
            );
        }
        // Validate callable
        if (is_string($callable)) {
            // ::callable shorthand (use a method from parent caller class)
            if (Utils\Str::startsWith('::', $callable)) {
                $class = debug_backtrace()[1]['class'];
                $callable = $class.$callable;
            } else {
                // Class::method shorthand (use a method from somewhere else in the namespace)
                if (Utils\Str::contains('::', $callable) && Utils\Str::contains('\\', $callable) == false) {
                    $explode = explode('\\', debug_backtrace()[1]['class']);
                    array_pop($explode);
                    $callable = implode('\\', $explode).'\\'.$callable;
                }
            }
        }
        if (is_callable($callable) == false) {
            throw new Exception(
                (new Message('%a argument is not a valid callable for validation task %t.'))
                    ->code('%t', $id)
                    ->code('%a', '$callable')
            );
        }
        $task = new stdClass();
        $task->value = $value;
        $task->callable = $callable;
        $task->message = $message ?? 'unspecified error.';
        // Reg the validation task
        $this->tasks[$id] = $task;
        ++$this->taskCount;

        return $this;
    }

    /**
     * Append method for grouped validator.
     *
     * @param string $id       the input identifier (sub-group)
     * @param mixed  $callable Callable (callable, string)
     * @param string $message  Message returned if the input doesn't validate. Use %v for input value, %s for htmlspecialchars value and %i for id.
     */
    public function append(string $id, $callable, string $message = null): self
    {
        if ($this->group == false) {
            throw new Exception(
                (new Message('Unable to append validation task %s, the validation group has not been defined.'))
                    ->code('%s', $id)
            );
        }
        // Validate $id
        Validation::single(
            '$id',
            $id,
            function (string $string): bool {
                return
                strlen($string) > 0
                && Utils\Str::startsWith(' ', $string) == false
                && Utils\Str::endsWith(' ', $string) == false;
            },
            "String %s must contain at least one character and it should't contain neither leading or trailing whitespace characters."
        );
        $this->add($this->group[0].' '.$id, $this->group[1], $callable, $message);

        return $this;
    }

    // /**
    //  * Append method for re-usable validator.
    //  *
    //  * @param string $id        The input identifier.
    //  * @param mixed $value Value to test agains for.
    //  */
    // public function append(string $id, $value) : self
    // {
    //     if ($this->reuse == false) {
    //         throw new Exception(
    //             (new Message('Unable to append validation task. Must provide %a in the object constructor like this: %c.'))
    //                 ->code('%a', '$callable')
    //                 ->code('%c', 'new ' . __CLASS__ . '($callable)')
    //         );
    //     }
    //     $this->add(...array_merge(func_get_args(), $this->reuse));
    //     return $this;
    // }

    /**
     * Execute the validation tasks.
     *
     * @throws ValidationException on invalidated tasks
     *
     * @return bool TRUE if validates
     */
    public function validate()
    {
        $this->validates = true;
        foreach ($this->tasks as $k => $task) {
            $this->results[$k] = [];
            $validate = true;
            $result = call_user_func($task->callable, $task->value) === true;
            $task->result = $result;
            $validate = $result;
            // Task results
            $result = new stdClass();
            $result->validates = $validate;
            $result->message = $validate == false ? (string) (new Message($task->message))
                ->code('%i', $k)
                ->code('%t', gettype($task->value))
                ->code('%v', $task->value)
                ->code('%s', htmlspecialchars($task->value))
            : 'OK';
            $this->results[$k] = $result;
            array_push($this->{$validate ? 'valid' : 'invalid'}, $k);
            // Global result
            if ($validate == false && $this->validates == true) {
                $this->validates = $validate;
            }
        }
        if ($this->validates == false) {
            $aux = [];
            foreach ($this->invalid as $k => $v) {
                $aux[] = (new Message('Validation failure in %s: %e'))
                        ->b('%s', $v)
                        ->strtr('%e', $this->results[$v]->message);
            }
            $this->message = implode("\n", $aux);
            throw new ValidationException($this->message);
        }

        return true;
    }

    /**
     * Provides read-only access to all object properties.
     */
    public function __call($name, $args)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }
}
class ValidationException extends Exception
{
    // public function __construct(Exception $e)
    // {
    //     parent::__construct($e->getMessage());
    // }
}
