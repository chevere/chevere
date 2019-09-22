The following request updates the author of an article:

PATCH /articles/1/relationships/author HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": { "type": "people", "id": "12" }
}

The following request clears the author of the same article:

PATCH /articles/1/relationships/author HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": null
}