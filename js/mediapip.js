const imageElement = document.getElementById('image');
const canvasElement = document.getElementById('canvas');
const canvasCtx = canvasElement.getContext('2d');

// 이미지 로드 및 적용
function loadImage() {
    const imageUrl = 'uploads/Palm_right<?echo $user_code; ?>/<?echo $board[right_palm_img]?>'; // 여기에 이미지 URL을 지정해주세요.
    imageElement.src = imageUrl;
    imageElement.onload = async () => {
        await detectHands();
    };
}

// 미디어파이프 핸드 라이브러리 사용 준비
async function loadHandpose() {
    const handpose = await handpose.load();
    console.log("Handpose model ready.");
    return handpose;
}

// 손 인식 및 그리기
async function detectHands() {
    const handpose = await loadHandpose();

    handpose.estimateHands(imageElement)
        .then(predictions => {
            predictions.forEach(prediction => {
                // 그리기 작업 (예: 손가락 좌표에 점 그리기)
            });
        });
}

loadImage();