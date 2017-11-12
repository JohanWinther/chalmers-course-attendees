# chalmers-course-attendees
Public API for lookup of course attendees at Chalmers

# API
## **Show Attendees**
  Returns JSON data of users of a course.

* **URL**

  `/<Course code>/<term>/<extra>`

* **Method:**

  `GET`

*  **URL Params**

   **Required:**

   `Course code` - Case insensitive course code in format *abcxxx*

   `term` - Study term in format *aYY* where *a* can be `v` (spring) or `h` (autumn) and *YY* is the year

   **Optional**

   `extra` - Set to `noarchive` to list users from that year that are still registered

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
    **Content:** `Course or term not found.` <br />
    **Explanation:** The course and year combination was not found.

  OR

  * **Code:** 403 Forbidden <br />
    **Content:** `Could not bind to directory.` <br />
    **Explanation:** Could not bind to the directory anonymously. This should normally work, but the server could have changed permissions.

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/tma970/h17",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```
