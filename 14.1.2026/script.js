// ===== VARIABLES =====
const button = document.getElementById('MyButton'); // Get reference to the button element with id "MyButton"
const input = document.getElementById('MyInput'); // Get reference to the input text field with id "MyInput"

// ===== EVENT LISTENERS =====
button.addEventListener('click', function() { // Attach a click event listener to the button
    alert(input.value); // When button is clicked, show an alert with the input field's text value
});

// ===== TABLE SETUP =====
const tbody = document.querySelector("#tabulka tbody"); // Get reference to the table body element where we'll add rows

// ===== FETCH JSON DATA =====
fetch("data.json") // Start fetching the data.json file from the server
  .then(response => response.json()) // Convert the response to JSON format
  .then(data => { // Once data is loaded, process it
    data.ucitele.forEach(zaznam => { // Loop through each teacher in the data array
      const tr = document.createElement("tr"); // Create a new table row for each teacher

      // ===== CREATE FIRST NAME CELL =====
      const tdJmeno = document.createElement("td"); // Create a new table cell for first name
      tdJmeno.textContent = zaznam.jmeno; // Set the cell's text content to the person's first name
      tr.appendChild(tdJmeno); // Add the first name cell to the table row

      // ===== CREATE LAST NAME CELL =====
      const tdPrijmeni = document.createElement("td"); // Create a new table cell for last name
      tdPrijmeni.textContent = zaznam.prijmeni; // Set the cell's text content to the person's last name
      tr.appendChild(tdPrijmeni); // Add the last name cell to the table row

      // ===== CREATE AGE CELL =====
      const tdVek = document.createElement("td"); // Create a new table cell for age
      tdVek.textContent = zaznam.vek; // Set the cell's text content to the person's age
      tr.appendChild(tdVek); // Add the age cell to the table row
      
      tbody.appendChild(tr); // Add the complete row (with all 3 cells) to the table body
    });
  })
  .catch(err => console.error("Chyba při načítání JSON:", err)); // If there's an error loading the JSON file, catch it and log to console
