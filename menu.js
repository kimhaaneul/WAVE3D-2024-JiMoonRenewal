export function loadMenu() {
    return `
       <div id="home-nav">
            <div id="home-left">
                <a href="/index.html">
                    <img src="/img/aijimoon-logo.png" width="224" height="30" decoding="async" data-nimg="" id="logoicon">
                </a>
                </div>
            <ul id="menu">
                <li>회사소개
               
                    <ul class="submenu">
                     <div id="menu-colorbox"></div>
                        <div id="menusquarebox"></div>
                        
                        <li><a href="cap_introduction.html">회사소개</a></li>
                        <li><a href="cap_ci.html">CI</a></li>
                        <li><a href="directions.html">오시는길</a></li>
                    </ul>
                </li>
                <li>지문소개
                    <ul class="submenu">
                    <div id="menu-colorbox"></div>
                        <li><a href="fg_introduction.html">AI지문시스템</a></li>
                        <li><a href="fg_type.html">AI지문유형</a></li>
                        <li><a href="fg_history.html">지문의역사</a></li>
                    </ul>
                </li>
                <li>프로그램
                    <ul class="submenu">
                    <div id="menu-colorbox"></div>
                        <li><a href="fg_program.html">AI지문프로그램</a></li>
                    </ul>
                </li>
                <li>가맹계약
                    <ul class="submenu">
                    <div id="menu-colorbox"></div>
                        <li><a href="inquiry.html">계약문의</a></li>

                    </ul>
                </li>
                <li>FAQ
                    <ul class="submenu">
                    <div id="menu-colorbox"></div>
                        <li><a href="fap.html">자주묻는질문</a></li>
                        <li><a href="inquiry.html">문의하기</a></li>
                    </ul>
                </li>
            </ul>
        
            <div id="home-right">
                <button id="login-go">
                    로그인               
                </button>
                <button id="join-go">
                    회원가입
                    <a href="#"></a>
                </button>
        </div>
    </div>
    `;
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("login-go").addEventListener("click", function() {
        window.location.href = "http://aitms.co.kr/capfingers/adminLogin.php";
    });

    document.getElementById("join-go").addEventListener("click", function() {
        window.location.href = "join.html"; // 회원가입 페이지 URL로 변경하세요
    });
});
