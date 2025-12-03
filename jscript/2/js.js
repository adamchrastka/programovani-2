let numbers = [];
let input;
let maxNumber = 0;
let average = 0;
let evenCount = 0;
let oddCount = 0;

while (true){
    input = prompt("Zadejte sve cislo:");
    if (input == '0') {
        alert("Konec");
        break;
    }

let number = parseFloat(input);
    if (isNaN(number)){
        alert("neni cislo");
        continue;
    }
    else {
        numbers.push(number);
    }

if (numbers.length > 0){
    maxNumber = Math.max(...numbers);
    

    let sum = numbers.reduce((a,b) => a + b, 0);
    average = sum / numbers.length;
}
    if (number % 2 == 1){
        oddCount++;
    }
    else if (number % 2 == 0){
        evenCount++;
    }
}
console.log("Nejvetsi cislo: ", maxNumber);
console.log("prumer: ", average);
console.log("pocet sudych: ", evenCount);
console.log("pocet lichych: ", oddCount);

if (numbers.length == 0){
    alert("Nebyla zadana zadna cisla");
    console.log(" ");
    console.log(" ");
    console.log("nebyla zadana zadna cisla");
}