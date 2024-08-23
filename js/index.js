
// 그라데이션



// 섹션2 : 버튼 클릭시 캐릭터 이동
document.addEventListener("DOMContentLoaded", () => {
    const leftButton = document.getElementById("left-button");
    const rightButton = document.getElementById("right-button");
    const jimoonbox = document.querySelector(".jimoonbox");
    const images = jimoonbox.querySelectorAll("img");

    let currentIndex = 0; // 현재 보여지는 첫 번째 이미지의 인덱스
    const maxVisibleImages = 6; // 한 번에 보여지는 이미지 수
    const imageWidth = 195; // 각 이미지의 너비
    const totalImages = images.length; // 총 이미지 수

    // 오른쪽 버튼 클릭 시
    rightButton.addEventListener("click", () => {
        if (currentIndex < totalImages - maxVisibleImages) {
            currentIndex++;
            images.forEach((img) => {
                img.style.transform = `translateX(-${currentIndex * imageWidth}px)`;
            });
        }
    });

    // 왼쪽 버튼 클릭 시
    leftButton.addEventListener("click", () => {
        if (currentIndex > 0) {
            currentIndex--;
            images.forEach((img) => {
                img.style.transform = `translateX(-${currentIndex * imageWidth}px)`;
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("jimoonbtn").addEventListener("click", function(){
        window.location.href = "fg_introduction.html";
    })
})
