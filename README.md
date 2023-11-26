# Chevere

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/chevere/test.yml?branch=4.0&style=flat-square)](https://github.com/chevere/chevere/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/chevere?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/chevere?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Fchevere%2F2.0)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/chevere/4.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_chevere&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_chevere)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/chevere/badge)](https://www.codefactor.io/repository/github/chevere/chevere)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/b956754f8ff04aaa9ca24a6e4cc21661)](https://app.codacy.com/gh/chevere/chevere/dashboard)

## Build from source

* Install vendor dependencies using [Composer](https://getcomposer.org):

```sh
composer install
```

## Scripts

* Run all tests:

```sh
composer all
```

* Run some tests:

```sh
composer phpstan
composer test
composer test-coverage
composer infection
```

* Update code style

```sh
composer cs-update
```

* Fix code-style

```sh
composer cs-fix
```

* Run with options:

```sh
composer <command> -- <options>
```

## Local GitHub workflow

* Requires [act](https://github.com/nektos/act)

Run the following command to execute the GitHub Workflow on local.

### ARM64 (Apple M1*)

```sh
act -P ubuntu-latest=shivammathur/node:latest-arm64v8 --container-architecture linux/arm64
```

### AMD64

```sh
act -P ubuntu-latest=shivammathur/node:latest --container-architecture linux/amd64
```

## Documentation

Documentation available at [chevere.org](https://chevere.org/).

## License

Copyright 2023 [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
