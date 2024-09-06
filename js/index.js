document.addEventListener("DOMContentLoaded", () => {
    const leftButton = document.getElementById("left-button");
    const rightButton = document.getElementById("right-button");
    const jimoonbox = document.querySelector(".jimoonbox");
    const images = jimoonbox.querySelectorAll("img");

    let currentIndex = 0; // 현재 보여지는 첫 번째 이미지의 인덱스
    let maxVisibleImages = 6; // 한 번에 보여지는 기본 이미지 수
    let imageWidth = 195; // 각 이미지의 기본 너비
    const totalImages = images.length; // 총 이미지 수

    // 해상도 감지 및 크기 조정
    const adjustImageSize = () => {
        const mediaQuery1025to1500 = window.matchMedia("(min-width: 1025px) and (max-width: 1500px)");
        const mediaQuery800to1024 = window.matchMedia("(min-width: 800px) and (max-width: 1024px)");
        const mediaQuery600to799 = window.matchMedia("(min-width: 600px) and (max-width: 799px)");
        const mediaQuery581to599 = window.matchMedia("(min-width: 581px) and (max-width: 599px)");
        const mediaQueryBelow581 = window.matchMedia("(max-width: 580px)");

        if (mediaQueryBelow581.matches) {
            // 해상도가 580px 이하일 때 설정
            imageWidth = 180; // 이미지 너비를 180px로 설정
            maxVisibleImages = 1; // 한 번에 보여지는 이미지 수를 1개로 설정
        } else if (mediaQuery581to599.matches) {
            // 해상도가 581px ~ 599px일 때 설정
            imageWidth = 180; // 이미지 너비를 180px로 설정
            maxVisibleImages = 2; // 한 번에 보여지는 이미지 수를 2개로 설정
        } else if (mediaQuery600to799.matches) {
            // 해상도가 600px ~ 799px일 때 설정
            imageWidth = 180; // 이미지 너비를 180px로 설정
            maxVisibleImages = 4; // 한 번에 보여지는 이미지 수를 4개로 설정
        } else if (mediaQuery800to1024.matches) {
            // 해상도가 800px ~ 1024px일 때 설정
            imageWidth = 180; // 이미지 너비를 180px로 설정
            maxVisibleImages = 3; // 한 번에 보여지는 이미지 수를 3개로 설정
        } else if (mediaQuery1025to1500.matches) {
            // 해상도가 1025px ~ 1500px일 때 설정
            imageWidth = 185; // 이미지 너비를 185px로 설정
            maxVisibleImages = 5; // 한 번에 보여지는 이미지 수를 5개로 설정
        } else {
            // 기본 설정
            imageWidth = 195; // 기본 이미지 너비로 설정
            maxVisibleImages = 6; // 기본 이미지 개수로 설정
        }

        // 이미지 크기 및 개수 조정에 따라 위치 재조정
        images.forEach((img, index) => {
            img.style.transform = `translateX(-${currentIndex * imageWidth}px)`;
        });
    };

    // 페이지 로드 시 이미지 크기 조정 함수 호출
    adjustImageSize();

    // 창 크기 변경 시 이미지 크기 조정 함수 호출
    window.addEventListener("resize", adjustImageSize);

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
    });
});
