The following request replaces every tag for an article:

PATCH /articles/1/relationships/tags HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": [
{ "type": "tags", "id": "2" },
{ "type": "tags", "id": "3" }
]
}

The following request clears every tag for an article:

PATCH /articles/1/relationships/tags HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": []
}