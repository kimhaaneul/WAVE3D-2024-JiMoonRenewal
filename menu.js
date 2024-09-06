export function loadMenu() {
    return `
        <div id="home-nav">
            <div id="home-left">
                <a href="/index.html">
                    <img src="/img/aijimoon-logo.png"/>
                </a>
            </div>

            <div id="hamburger-menu" class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul id="menu">  
                <li>회사소개
                    <ul class="submenu"> 
                        <li id="menu-colorbox"></li>  
                        <li><a href="cap_introduction.html">회사소개</a></li>
                        <li><a href="cap_ci.html">CI소개</a></li>    
                    </ul>
                </li>
                <li>지문소개
                    <ul class="submenu">
                        <li id="menu-colorbox"></li> 
                        <li><a href="fg_introduction.html">AI지문시스템</a></li>
                        <li><a href="fg_type.html">AI지문유형</a></li>
                        <li><a href="fg_history.html">AI지문의역사</a></li>
                    </ul>
                </li>
                <li>프로그램
                    <ul class="submenu">
                        <li id="menu-colorbox"></li> 
                        <li><a href="fg_program.html">AI지문프로그램</a></li>
                    </ul>
                </li>
                <li>센터계약
                    <ul class="submenu">
                        <li id="menu-colorbox"></li> 
                        <li><a href="center.html">센터계약</a></li>
                    </ul>
                </li>
                <li>FAQ
                    <ul class="submenu">
                        <li id="menu-colorbox"></li> 
                        <li><a href="fap.html">자주묻는질문</a></li>
                        <li><a href="inquiry.html">문의하기</a></li>
                    </ul>
                </li>
                <li><a href="#" id="login-go">로그인</a></li>   
                <li><a href="#" id="join-go">회원가입</a></li>
            </ul>
        </div>
    `;
}

document.addEventListener("DOMContentLoaded", function() {

document.getElementById("login-go").addEventListener("click", function(event) {
    window.location.href = "http://aitms.co.kr/capfingers/adminLogin.php";
});

// 회원가입 버튼 클릭 시 페이지 이동
document.getElementById("join-go").addEventListener("click", function(event) {
    window.location.href = "http://aitms.co.kr/capfingers/CMS_register_1.php";
});

    // 햄버거 메뉴 클릭 시 메뉴 보이기/숨기기
    const hamburgerMenu = document.getElementById("hamburger-menu");
    const menu = document.getElementById("menu");

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener("click", function() {
            this.classList.toggle("active");
            menu.classList.toggle("active");
        });
    }

    // 서브메뉴 열고 닫기 기능 추가
    const menuItems = document.querySelectorAll("#menu > li");

    menuItems.forEach(item => {
        item.addEventListener("click", function() {
            const submenu = this.querySelector(".submenu");
            if (submenu) {
                if (submenu.classList.contains("submenu-active")) {
                    submenu.classList.remove("submenu-active");
                    submenu.style.maxHeight = null; // 서브메뉴 닫기
                } else {
                    // 모든 서브메뉴 닫기
                    document.querySelectorAll(".submenu").forEach(sub => {
                        sub.classList.remove("submenu-active");
                        sub.style.maxHeight = null;
                    });
                    // 클릭한 서브메뉴 열기
                    submenu.classList.add("submenu-active");
                    submenu.style.maxHeight = submenu.scrollHeight + "px"; // 서브메뉴 열기
                }
            }
        });
    });
});
