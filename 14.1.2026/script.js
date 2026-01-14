// promenne
const button = document.getElementById('MyButton'); // ziskame referenci na tlacitko
const input = document.getElementById('MyInput');   // ziskame referenci na pole pro input/zadani textu

// udalosti
button.addEventListener('click', function() { //priradime udalost click na tlacitko
    alert(input.value); // pri kliknuti zobrazime alert s hodnotou z input pole
});


const tbody = document.querySelector("#tabulka tbody");

// ===== FETCH JSON DATA =====
// Start fetching the data.json file from the server
fetch("data.json")
  // Convert the response to JSON format
  .then(response => response.json())
  // Once data is loaded, process it
  .then(data => {
    // Loop through each record (person) in the data array
    data.forEach(zaznam => {
      // Create a new table row for each person
      const tr = document.createElement("tr");

      // ===== CREATE FIRST NAME CELL =====
      // Create a new table cell for first name
      const tdJmeno = document.createElement("td");
      // Set the cell's text content to the person's first name
      tdJmeno.textContent = zaznam.jmeno;
      // Add the first name cell to the table row
      tr.appendChild(tdJmeno);

      // ===== CREATE LAST NAME CELL =====
      // Create a new table cell for last name
      const tdPrijmeni = document.createElement("td");
      // Set the cell's text content to the person's last name
      tdPrijmeni.textContent = zaznam.prijmeni;
      // Add the last name cell to the table row
      tr.appendChild(tdPrijmeni);

      // ===== CREATE AGE CELL =====
      // Create a new table cell for age
      const tdVek = document.createElement("td");
      // Set the cell's text content to the person's age
      tdVek.textContent = zaznam.vek;
      // Add the age cell to the table row
      tr.appendChild(tdVek);
      
      // Add the complete row (with all 3 cells) to the table body
      tbody.appendChild(tr);
    });
  })
  // If there's an error loading the JSON file, catch it and log to console
  .catch(err => console.error("Chyba při načítání JSON:", err));
