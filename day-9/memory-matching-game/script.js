const grid = document.querySelector(".grid");
const movesCounter = document.getElementById("moves");
const resetBtn = document.getElementById("reset-btn");
const message = document.getElementById("message");

let cards = [];
let flippedCards = [];
let moves = 0;

const cardEmojis = ["♥️","♦️","♠️","♣️","🔆","🌍","🚩","🌈"];

function initGame(){
    grid.innerHTML = "";
    moves = 0;
    movesCounter.textContent = moves;
    flippedCards = [];

    const gameCards = [...cardEmojis, ...cardEmojis].sort(() => Math.random() - 0.5);

    gameCards.forEach((emoji) => {
        const card = document.createElement("div");
        card.classList.add("card");
        card.textContent =emoji;
        card.addEventListener("click", handleCardClick);
        grid.appendChild(card);
    });

    cards = Array.from(document.querySelectorAll(".card"));
}

function handleCardClick(e){
    const clickedCard = e.target;

    if(
        clickedCard.classList.contains("flipped") ||
        clickedCard.classList.contains("matched")){
            return;
        }

        clickedCard.classList.add("flipped");
        flippedCards.push(clickedCard);
        
        if(flippedCards.length === 2){
            moves++;
            movesCounter.textContent = moves;

            const [card1, card2] = flippedCards;

        if(card1.textContent === card2.textContent){
            card1.classList.add("matched");
            card2.classList.add("matched");
            flippedCards = [];

            const matchedCards = document.querySelectorAll(".card.matched");
            if(matchedCards.length === cards.length){
                message.textContent = `🎉 Congratulations! You finished in ${moves} moves!`;
            }
        }else {
            setTimeout(() => {
                card1.classList.remove("flipped");
                card2.classList.remove("flipped");
                flippedCards = [];
            }, 800);
        }

        }
    }

    resetBtn.addEventListener("click", initGame);

    initGame();