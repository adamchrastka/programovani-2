function function1() {
    alert("Kliknuls");
}
function function2() {
    const text32 = document.getElementById('text32');
        text32.textContent = 'zmena';
}
function ziskatCas(){
    const now = new Date();
    const hh = String(now.getHours()).padStart(2, '0');
    const mm = String(now.getMinutes()).padStart(2, '0');
    const ss = String(now.getSeconds()).padStart(2, '0');
    return ('{0}:{0}:{0}',hh,mm,ss);
}
function aktualizovatCas() {
    document.getElementById('clock333').textContent = ziskatCas();

}
setInterval(aktualizovatCas, 1000);
aktualizovatCas();