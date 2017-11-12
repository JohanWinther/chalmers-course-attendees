# chalmers-course-attendees
Public API for lookup of course attendees at Chalmers

# API
## **Show Attendees**
  Returns JSON data of users of a course.

* **URL**

  /?course=id&term=axx

* **Method:**

  `GET`

*  **URL Params**

   **Required:**

   `id=abcxxx` (Course code)

   `axx=v|hxx` (v = spring, h = autumn, xx = year)

* **Data Params**

  None

* **Success Response:**

  * **Code:** 200 <br />
    **Content:**
    ``` json
    {   
        "list": [
            {
                "given_name": "Emil",
                "surname": "Emilsson",
                "full_name": "Emil Emilsson",
                "email": "emil@student.chalmers.se",
                "cid": "emil"
            },
            {
                "given_name": "Emilia",
                "surname": "Emilsson",
                "full_name": "Emilia Emilsson",
                "email": "emilia@student.chalmers.se",
                "cid": "emilia"
            }
        ],
        "attendees": 2
    }
    ```

* **Error Response:**

  * **Code:** 404 NOT FOUND <br />
    **Content:** none <br />
    **Explanation:** The course and year combination was not found.

  OR

  * **Code:** 403 Forbidden <br />
    **Content:** none <br />
    **Explanation:** Could not bind to the directory anonymously. This should normally work.

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/?course=tma970&term=h17",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```
