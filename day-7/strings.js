//string
//templates - `` - it can use for multiline strings, variables - ${}, to write html , to use single & double quotes

let firstName = "John";
let lastName = "Doe";

let txt = `Welcome ${firstName}, ${lastName}!`;

console.log(txt);


let price = 10;
let VAT = 0.25;

let total = `Total: ${(price * (1 + VAT)).toFixed(2)}`;

console.log(total);


//backlash escape character(\)

// \', \", \\  
// \b => backspace,
console.log("Hello\bWorld"); 

// \f => used in printing, 
// \n => new line,
 console.log("Hello\nWorld");

// \r => overwrite content before this character for ex: 
console.log("Hello\rWorld");

// \t => horizontal tab
console.log("Name\tAge\tCity");
console.log("Sneha\t21\tAhmedabad");

// \v => vertical tab
console.log("Line1\vLine2");


//If no character is found, [ ] returns undefined, while charAt() returns an empty string.

//All string methods return a new string. They don't modify the original string. (strings are immutable.)

/* substring() is similar to slice().
The difference is that start and end values less than 0 are treated as 0 in substring() */