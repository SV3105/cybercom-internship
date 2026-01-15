const products = [
    {id:1, name: "shoes", price: 2000, inStock: true},
    {id:2, name: "bag", price: 1500, inStock: true},
    {id:3, name: "watch", price: 3000, inStock: false},
    {id:4, name: "shirt", price: 800, inStock: true}
];

const userProfile = {
    name: "Sneha",
    email: "sneha@gmail.com",
    city: "Ahmedabad"
};

//for loop - when known count - pagination, carousel, sliders
console.log("FOR LOOP (pagination):");

const productsPerPage = 2;
const currentPage = 1;

const start = (currentPage - 1) * productsPerPage;

const end = start + productsPerPage;

for(let i= start; i<end && i<products.length; i++){
    console.log(products[i].name);    
}

//while - when unknown count - payment , user login attemptes

console.log("\nWHILE LOOP (payment ready):");

let paymentStatus = "pending";

while(paymentStatus !== "success"){
    console.log("Retrying payment...");
    paymentStatus = "success";    
}

//do...while loop - show coupon once, user input validation

console.log("\nDO WHILE LOOP (show coupon once):");

let couponShown = false;

do {
  console.log("Showing coupon popup");
  couponShown = true;
} while (!couponShown);

//for...of loop- it traverse array, product listing, order items, cart items

console.log("\nFOR OF (product listing):");

for(let product of products){
    console.log(product.name, product.price);
}

//for...in user profile traversal, used in objects

console.log("\nFOR IN (user profile):");

for(let key in userProfile){
    console.log(key + ":" , userProfile[key]);
}

//for...Each -- it is not a loop, array method, to apply logic on every items 
//no break, continue , no async-await, no return statement - cannot be stopped 
console.log("\nFOREACH (render products):");

products.forEach(product => {
    console.log("Render product:", product.name);
})

//filter --- array method, select in-stock products only, it creates new array with items that pass a condition

console.log("\nFILTER (in-stock products):");

const inStockProducts = products.filter(p => p.inStock);
console.log(inStockProducts);

//map- creates new array by applying logic on each item , modify item, convert price to gstprice

console.log("\nMAP (apply discount)");

const discountedPrices = inStockProducts.map(p => ({
    ...p,
    price: p.price * 0.9
}));

console.log(discountedPrices);

//reduce- gives sum of array values

console.log("\nREDUCE (cart total):");

const totalOriginal = inStockProducts.reduce((sum, p) => sum + p.price, 0);
console.log("Original total:", totalOriginal);


const cartTotal = discountedPrices.reduce((total, product) => {
    return total + product.price;
}, 0);

console.log("Discounted amount:", cartTotal);

const savings = totalOriginal - cartTotal;

console.log("You saved: ", savings);






