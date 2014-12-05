#Breaking CORS

Use it only for testing API.

## API

**GET | POST | PUT** `/call`

**Parameters**

`* required`

Name   | Description | Default            | Example
------ | ----------- | ------------------ | -----------------------
url *  | API url     | -                  | http://randomuser.me/g/
method | -           | GET (GET or POST)  | POST
data   | Parameters  | null               | {"seed": "awesome"}
type   | -           | json (json or xml) | xml

**Warning:** `data` must be a **json**


## Usage

Some examples:

### curl

    curl -X POST http://cors.lagden.in/call -i -d "url=http://randomuser.me/g/&method=POST&data={\"seed\":\"awesome\"}"

### javascript

    var formData,
        xhr,
        listener;

    listener = function() {
        console.log(xhr.responseText);
    };

    formData = new FormData();
    formData.append('url', 'http://randomuser.me/g/');
    formData.append('data', '{"seed": "awesome"}');

    xhr = new XMLHttpRequest();
    xhr.onload = listener;
    xhr.open('POST', 'http://cors.lagden.in/call');
    xhr.send(formData);

## Contributors

- [Thiago Lagden](http://lagden.in)

:beers: