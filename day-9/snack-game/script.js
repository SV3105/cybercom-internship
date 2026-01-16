const grid = document.querySelector(".grid");
const scoreEl = document.getElementById("score");
const startBtn = document.getElementById("start-btn");

const width = 20;
let cells = [];
let snake = [2,1,0];
let direction = 1;
let foodIndex;
let score = 0;
let interval = null;
let speed = 300;

for(let i=0; i< width * width; i++){
    const cell = document.createElement("div");
    cell.classList.add("cell");
    grid.appendChild(cell);
    cells.push(cell);
}

function drawSnake(){
    cells.forEach(cell => cell.classList.remove("snake", "snake-head"));

snake.forEach((index, i) => {
    if(i === 0){
        cells[index].classList.add("snake-head");
    }
    else{
        cells[index].classList.add("snake");
    }
});
}

function generateFood(){
    do {
        foodIndex = Math.floor(Math.random()*cells.length);
    }
    while (snake.includes(foodIndex));
    cells.forEach(cell => cell.classList.remove("food"));
    cells[foodIndex].classList.add("food");
}

function moveSnake(){
    const head = snake[0];
    const tail =  snake[snake.length - 1];

    if(
        (direction === 1 && (head % width === width - 1)) || // right wall
    (direction === -1 && (head % width === 0)) ||       // left wall
    (direction === width && head + width >= width*width) || // bottom wall
    (direction === -width && head - width < 0) ||          // top wall
    snake.includes(head + direction)
    ){
        clearInterval(interval);
    alert(`Game Over! Score: ${score}`);
    return;
    }

    snake.unshift(head + direction);

    if(snake[0] === foodIndex){
        score++;
        scoreEl.textContent = score;
        generateFood();
    }
    else{
        snake.pop();
    }

    drawSnake();
}

function control(e) {
  if (e.key === "ArrowUp" && direction !== width) direction = -width;
  else if (e.key === "ArrowDown" && direction !== -width) direction = width;
  else if (e.key === "ArrowLeft" && direction !== 1) direction = -1;
  else if (e.key === "ArrowRight" && direction !== -1) direction = 1;
}

function startGame(){
     snake = [2, 1, 0];
  direction = 1;
  score = 0;
  scoreEl.textContent = score;

  clearInterval(interval);
  drawSnake();
  generateFood();

  interval = setInterval(moveSnake, speed);
}

document.addEventListener("keydown", control);
startBtn.addEventListener("click", startGame);

drawSnake();
generateFood();