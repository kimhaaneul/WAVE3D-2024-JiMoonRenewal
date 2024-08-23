const faqItems = document.querySelectorAll('.faq-list-li');

faqItems.forEach(item => {
    const question = item.querySelector('.faq-q');
    question.addEventListener('click', (event) => {
        event.stopPropagation(); 
        faqItems.forEach(i => {
            if (i !== item) {
                i.classList.remove('active');
                i.querySelector('.faq-a').style.maxHeight = null;
            }
        });

        item.classList.toggle('active');

        const answer = item.querySelector('.faq-a');
        if (item.classList.contains('active')) {
            answer.style.maxHeight = answer.scrollHeight + 'px';
        } else {
            answer.style.maxHeight = null;
        }
    });
});

// 빈 화면 클릭 시 모든 FAQ 항목 접기
document.body.addEventListener('click', () => {
    faqItems.forEach(item => {
        item.classList.remove('active');
        item.querySelector('.faq-a').style.maxHeight = null;
    });
});

// 페이지 번호 클릭시
const itemsPerPage = 5;
let currentPage = 1;
const totalPages = Math.ceil(faqItems.length / itemsPerPage);

function renderPage(page) {
    // 모든 항목 숨기기
    faqItems.forEach((item, index) => {
        item.style.display = 'none';
    });

    // 현재 페이지의 항목만 보이기
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    for (let i = startIndex; i < endIndex && i < faqItems.length; i++) {
        faqItems[i].style.display = 'block';
    }

    // 이전, 다음 버튼 상태 업데이트
    document.getElementById('prev').disabled = (page === 1);
    document.getElementById('next').disabled = (page === totalPages);

    // 페이지 버튼 상태 업데이트
    document.querySelectorAll('.page-num').forEach((button, index) => {
        button.classList.toggle('active', index + 1 === page);
    });
}

// 페이지 번호 클릭 이벤트
document.querySelectorAll('.page-num').forEach((button, index) => {
    button.addEventListener('click', () => {
        currentPage = index + 1;
        renderPage(currentPage);
    });
});

// 이전 버튼 클릭
document.getElementById('prev').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        renderPage(currentPage);
    }
});

// 다음 버튼 클릭
document.getElementById('next').addEventListener('click', () => {
    if (currentPage < totalPages) {
        currentPage++;
        renderPage(currentPage);
    }
});

// 초기 페이지 렌더링
renderPage(currentPage);
