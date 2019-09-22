Comments with IDs of 12 and 13 are removed from the list of comments for the article with ID 1:

DELETE /articles/1/relationships/comments HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": [
{ "type": "comments", "id": "12" },
{ "type": "comments", "id": "13" }
]
}