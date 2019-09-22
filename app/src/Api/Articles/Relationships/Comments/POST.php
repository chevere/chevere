The comment with ID 123 is added to the list of comments for the article with ID 1:

POST /articles/1/relationships/comments HTTP/1.1
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json

{
"data": [
{ "type": "comments", "id": "123" }
]
}