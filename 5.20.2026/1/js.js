function calculateArea() {
    let radius = document.getElementById("radius").value;
    let area = Math.PI * radius * radius;
    document.getElementById("result").innerText = area.toFixed(2);
    document.getElementById("circumference").innerText = (2 * Math.PI * radius).toFixed(2);
}

function calculateRectangle() {
    let sideA = parseFloat(document.getElementById("sideA").value);
    let sideB = parseFloat(document.getElementById("sideB").value);
    let area = sideA * sideB;
    let perimeter = 2 * (sideA + sideB);
    document.getElementById("rectangleArea").innerText = area.toFixed(2);
    document.getElementById("rectanglePerimeter").innerText = perimeter.toFixed(2);
}
