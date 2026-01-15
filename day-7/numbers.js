// isNaN() -> is not a number

let x = 100 / "Apple";

console.log(isNaN(x));

console.log(typeof(NaN));

//numeric constants if preceded by 0x it will interpreted as hexadecimal

//0xFF = 255

//number with leading zero (07) it will interpret as octal 

//toFixed() --> returns a string, rounds a number to given digits

let y = 9.656;

console.log(y.toFixed(0));
console.log(y.toFixed(2));

//toPrecision() --> returns a string of number with a specified length

console.log(y.toPrecision(6));
console.log(y.toPrecision(4));

//Number() -> it converts string, boolean , null into number

//parseInt() -> forgiving extraction
//reads from left to right, stops at 1st non-number, returns as an integer

// "" = Number() = 0 , parseInt = NaN
// true = Number() = 1, parseInt = NaN


