$(document).ready(function () {
    // 컨테이너 전환을 담당하는 함수
    function showContainer(containerId) {
        $('.container-box').removeClass('active'); // 모든 컨테이너 숨기기
        $('#' + containerId).addClass('active'); // 선택한 컨테이너만 표시
        history.replaceState(null, null, '#' + containerId); // 해시 추가
    }

    function activateSidebarButton(button) {
        $('.sidebar a').removeClass('active'); // 모든 사이드바 링크에서 active 클래스 제거
        button.addClass('active'); // 클릭된 버튼에 active 클래스 추가

        // 클릭된 버튼의 ID를 로컬 스토리지에 저장
        localStorage.setItem('activeSidebarButton', button.attr('id'));
    }

    // 모달 초기화: 모달을 기본적으로 숨긴 상태로 설정하고, 백드롭을 static으로 설정
    $('#addConsultantModal').modal({
        show: false,
        backdrop: 'static'
    });

    // "내 이벤트 정보" 업데이트 함수 (Ajax 사용)
    function updateMyEvents(page = 1) {
        $.ajax({
            url: 'get_my_events.php',
            type: 'GET',
            data: { page: page },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const events = response.data;
                    const $tableBody = $('#myEventsContainer tbody');
                    $tableBody.empty(); // 기존 데이터를 비움

                    if (events.length > 0) {
                        events.forEach(function (event) {

                            const formattedDate = event.date.split(' ')[0]; // "YYYY-MM-DD HH:MM:SS"에서 "YYYY-MM-DD" 부분만 사용
                            const formattedSubmittedAt = event.submitted_at.split(' ')[0]; // "YYYY-MM-DD HH:MM:SS"에서 "YYYY-MM-DD" 부분만 사용

                            const row = `
                         <tr>
                                <td>${event.location}</td>
                                <td>${formattedDate}</td> <!-- 날짜만 표시 -->
                                <td>${event.time}</td>
                                <td>${formattedSubmittedAt}</td> <!-- 날짜만 표시 -->
                                <td>${event.event_code}</td>
                                <td>
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#addConsultantModal"
                                                data-idx="${event.idx}"
                                                data-event-code="${event.event_code}"
                                                ${event.pass === 'true' ? '' : 'disabled'}>등록</button>
                                        </div>
                                    </td>
                                   <td>
    ${event.pass === 'true' 
        ? '1차 승인 완료/<br>최종 승인 대기' 
        : (event.pass === 'complete' 
            ? '최종 승인 완료' 
            : '신청 완료/<br>1차 승인 대기')}
</td>

                                    <td>
                                        <button class="btn btn-secondary" data-toggle="modal" data-target="#consultantModal"
                                            data-idx="${event.idx}" data-location="${event.location}"
                                            data-date="${event.date}" data-time="${event.time}"
                                            data-people_num="${event.people_num}"
                                            data-con_num="${event.con_num}"
                                            ${event.pass === 'true' ? '' : 'disabled'}>수정</button>
                                    </td>
                                    <td>
                                        ${event.event_code ? `<a href="reservation.php?event_code=${event.event_code}" target="_blank">예약자 정보폼</a>` : ''}
                                    </td>
                                </tr>`;
                            $tableBody.append(row);
                        });
                    } else {
                        $tableBody.append('<tr><td colspan="9">등록된 예약 정보가 없습니다.</td></tr>');
                    }

                    // 페이지 네비게이션 업데이트
                    const totalPages = response.total_pages;
                    const currentPage = page;
                    const $pagination = $('#myEventsContainer .pagination');
                    $pagination.empty();

                    if (totalPages > 1) {
                        const prevClass = currentPage <= 1 ? 'disabled' : '';
                        const nextClass = currentPage >= totalPages ? 'disabled' : '';

                        $pagination.append(`
                            <li class="page-item ${prevClass}">
                                <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">&laquo;</a>
                            </li>
                        `);

                        for (let i = 1; i <= totalPages; i++) {
                            const activeClass = i === currentPage ? 'active' : '';
                            $pagination.append(`
                                <li class="page-item ${activeClass}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>
                            `);
                        }

                        $pagination.append(`
                            <li class="page-item ${nextClass}">
                                <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">&raquo;</a>
                            </li>
                        `);

                        // 페이지 링크 클릭 이벤트 처리
                        $pagination.find('a').on('click', function (e) {
                            e.preventDefault();
                            const page = $(this).data('page');
                            localStorage.setItem('myEventsPage', page); // 현재 페이지를 로컬 스토리지에 저장
                            updateMyEvents(page); // 해당 페이지로 이동
                        });
                    }

                    // 페이지 로드 시 로컬 스토리지에서 페이지 번호를 가져와 활성화 상태 설정
                    const storedPage = localStorage.getItem('myEventsPage');
                    if (storedPage) {
                        $pagination.find(`[data-page="${storedPage}"]`).parent().addClass('active');
                    } else {
                        $pagination.find(`[data-page="${currentPage}"]`).parent().addClass('active');
                    }
                } else {
                    alert(response.message);
                }

                activateSidebarButton($('#myEventsBtn'));
                showContainer('myEventsContainer');
            },
            error: function (xhr, status, error) {
                alert('내 이벤트 정보를 가져오는 중 오류가 발생했습니다: ' + error);
            }
        });
    }

    // 사이드바 버튼 클릭 이벤트 처리
    $('#myPageBtn').click(function (e) {
        e.preventDefault();
        activateSidebarButton($(this));
        showContainer('myPageContainer');
    });

    $('#myEventsBtn').click(function (e) {
        e.preventDefault();
        activateSidebarButton($(this));
        updateMyEvents(); // 먼저 데이터 업데이트 실행
    });

    $('#applyEventBtn').click(function (e) {
        e.preventDefault();
        activateSidebarButton($(this));
        showContainer('applyEventContainer');
    });

    $('#participateEventsBtn').click(function (e) {
        e.preventDefault();
        activateSidebarButton($(this));
        updateParticipatedEvents(); // 참여 이벤트 데이터 업데이트 실행
    });

    // 페이지 로드 시 '내 이벤트 정보'를 기본적으로 표시하고 데이터 업데이트
    const myEventsPage = localStorage.getItem('myEventsPage') || 1;
    updateMyEvents(myEventsPage);

    // 페이지 로드 시 해시 값에 따라 컨테이너 표시, 없으면 '내 이벤트 정보'로 이동
    if (window.location.hash) {
        showContainer(window.location.hash.substring(1));
    } else {
        showContainer('myEventsContainer');
    }

    // "참여 이벤트" 업데이트 함수 (Ajax 사용)
    function updateParticipatedEvents(page = 1) {
        $.ajax({
            url: 'get_participated_events.php',
            type: 'GET',
            data: { event_page: page },
            dataType: 'json',
            success: function(response) {
                const events = response.events;
                const $tableBody = $('#participateEventsContainer tbody');
                $tableBody.empty();

                if (events.length > 0) {
                    events.forEach(function(event) {
                        const row = `
                            <tr>
                                <td>${event.location}</td>
                                <td>${event.date}</td>
                                <td>${event.time}</td>
                                <td>${event.event_code}</td>
                                <td><button class="btn btn-primary" onclick="viewReservations('${event.event_code}')">조회</button></td>
                            </tr>
                        `;
                        $tableBody.append(row);
                    });
                } else {
                    $tableBody.append('<tr><td colspan="5">참여한 이벤트가 없습니다.</td></tr>');
                }

                // 페이지 네비게이션 업데이트
                const totalPages = response.total_event_pages;
                const currentPage = page;
                const $pagination = $('#participateEventsContainer .pagination');
                $pagination.empty();

                if (totalPages > 1) {
                    const prevClass = currentPage <= 1 ? 'disabled' : '';
                    const nextClass = currentPage >= totalPages ? 'disabled' : '';

                    $pagination.append(`
                        <li class="page-item ${prevClass}">
                            <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">&laquo;</a>
                        </li>
                    `);

                    for (let i = 1; i <= totalPages; i++) {
                        const activeClass = i === currentPage ? 'active' : '';
                        $pagination.append(`
                            <li class="page-item ${activeClass}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `);
                    }

                    $pagination.append(`
                        <li class="page-item ${nextClass}">
                            <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">&raquo;</a>
                        </li>
                    `);

                    // 페이지 링크 클릭 이벤트 처리
                    $pagination.find('a').on('click', function(e) {
                        e.preventDefault();
                        const page = $(this).data('page');
                        localStorage.setItem('participateEventsPage', page); // 현재 페이지를 로컬 스토리지에 저장
                        updateParticipatedEvents(page); // 해당 페이지로 이동
                    });

                    // 페이지 로드 시 로컬 스토리지에서 페이지 번호를 가져와 활성화 상태 설정
                    const storedPage = localStorage.getItem('participateEventsPage');
                    if (storedPage) {
                        $pagination.find(`[data-page="${storedPage}"]`).parent().addClass('active');
                    } else {
                        $pagination.find(`[data-page="${currentPage}"]`).parent().addClass('active');
                    }
                }

                activateSidebarButton($('#participateEventsBtn'));
                showContainer('participateEventsContainer');
            },
            error: function(xhr, status, error) {
                console.error('참여 이벤트 목록을 가져오는 중 오류 발생: ' + error);
                alert('참여 이벤트 목록을 가져오는 중 오류가 발생했습니다.');
            }
        });
    }

    // 컨설턴트 추가 모달 처리
$('#addConsultantModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var reservationIdx = button.data('idx');
    var eventCode = button.data('event-code');
    console.log(reservationIdx, eventCode);  // 전달된 데이터 확인
    
    // 폼 필드 초기화
    $('#newConsultantCode').val('');
    
    // 기존 컨설턴트 목록 초기화
    $('#addConsultantList').empty();

    // 기존 컨설턴트 목록 로드
    $.ajax({
        url: 'get_consultants.php',
        type: 'POST',
        data: { event_code: eventCode },
        dataType: 'json',
        cache: false, // 캐시 비활성화
        success: function(response) {
            if (response.status === 'success') {
                var consultants = response.consultants;
                $('#addConsultantList').empty();  // 기존 목록 제거
                consultants.forEach(function(consultant) {
                    var newRow = `
                        <tr>
                            <td>${consultant.name}</td>
                            <td>${consultant.phone_number}</td>
                            <td>${consultant.consultant_code}</td>
                            <td><button class='btn btn-danger' onclick='deleteConsultant("${eventCode}", "${consultant.consultant_code}")'>삭제</button></td>
                        </tr>`;
                    $('#addConsultantList').append(newRow);
                });
            } else {
                alert('오류 발생: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert("오류 발생: " + error);
        }
    });

    // 컨설턴트 추가 버튼 클릭 처리
    $('#addConsultantButton').off('click').on('click', function() {
        var consultantCode = $('#newConsultantCode').val().trim();

        if (consultantCode === "") {
            alert("컨설턴트 코드를 입력해 주세요.");
            return;
        }

        $.ajax({
            url: 'add_consultants.php',
            type: 'POST',
            data: {
                idx: reservationIdx,  // 이 데이터는 서버에서 사용되지 않으므로 필요 없다면 제거 가능
                event_code: eventCode,
                consultant_code: consultantCode
            },
            dataType: 'json',
            success: function(response) {
                console.log(response); // 서버 응답 확인
                if (response.status === 'success') {
                    const newRow = $(`
                        <tr>
                            <td>${response.name}</td>
                            <td>${response.phone_number}</td>
                            <td>${consultantCode}</td>
                            <td><button class="btn btn-danger" onclick="deleteConsultant('${eventCode}', '${consultantCode}')">삭제</button></td>
                        </tr>
                    `);
                    $('#addConsultantList').append(newRow);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert("오류 발생: " + error);
            }
        });

        $('#newConsultantCode').val('');  // 입력란 초기화
    });
});

// 모달이 닫힐 때 상태 초기화
$('#addConsultantModal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset'); // 폼 필드 초기화
    $('#addConsultantList').empty(); // 목록 초기화
});

// 컨설턴트 삭제 처리 함수
window.deleteConsultant = function(eventCode, consultantCode) {
    $.ajax({
        url: 'delete_consultant.php',
        type: 'POST',
        data: { event_code: eventCode, consultant_code: consultantCode },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert('삭제가 성공적으로 완료되었습니다.');
                $(`#addConsultantList tr`).filter(function() {
                    return $(this).find('td').eq(2).text() === consultantCode;
                }).remove();  // 삭제된 행 제거
            } else {
                alert('삭제 중 오류가 발생했습니다: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert("오류 발생: " + error);
        }
    });
};



    // 예약자 조회 모달
    window.viewReservations = function(eventCode, page = 1) {
        $.ajax({
            url: 'get_reservations.php',
            type: 'GET',
            data: { event_code: eventCode, page: page },
            dataType: 'json',
            success: function(response) {
                const reservations = response.reservations;
                const $reservationList = $('#reservationList');
                $reservationList.empty(); // 기존 데이터를 비움

                if (reservations.length > 0) {
                    reservations.forEach(function(reservation) {
                        const row = `
                            <tr>
                                <td>${reservation.idx}</td>
                                <td>${reservation.reserver_name}</td>
                                <td>${reservation.phone}</td>
                                <td>${reservation.hand_s}</td>
                                <td>${reservation.gender}</td>
                                <td>${reservation.birth_date}</td>
                                <td>${reservation.mbti}</td>
                                <td>${reservation.reservation_time}</td>
                                <td>${reservation.pass ? '통과' : '대기'}</td>
                            </tr>`;
                        $reservationList.append(row);
                    });
                } else {
                    $reservationList.append('<tr><td colspan="9">예약자가 없습니다.</td></tr>');
                }

                // 페이지네이션 처리
                const totalPages = response.total_pages;
                const currentPage = page;
                const $pagination = $('#reservationPagination');
                $pagination.empty();

                if (totalPages > 1) {
                    const prevClass = currentPage <= 1 ? 'disabled' : '';
                    const nextClass = currentPage >= totalPages ? 'disabled' : '';

                    $pagination.append(`
                        <li class="page-item ${prevClass}">
                            <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">&laquo;</a>
                        </li>
                    `);

                    for (let i = 1; i <= totalPages; i++) {
                        const activeClass = i === currentPage ? 'active' : '';
                        $pagination.append(`
                            <li class="page-item ${activeClass}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `);
                    }

                    $pagination.append(`
                        <li class="page-item ${nextClass}">
                            <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">&raquo;</a>
                        </li>
                    `);

                    $pagination.find('a').on('click', function(e) {
                        e.preventDefault();
                        const newPage = $(this).data('page');
                        viewReservations(eventCode, newPage); // 해당 페이지로 이동
                    });
                }

                $('#reservationModal').modal('show'); // 모달 표시
            },
            error: function(xhr, status, error) {
                console.error('예약자 정보를 가져오는 중 오류 발생: ' + error);
                alert('예약자 정보를 가져오는 중 오류가 발생했습니다.');
            }
        });
    };
// 날짜 형식 검증 함수
function validateDateFormat(value) {
    var regex = /^\d{4}-\d{2}-\d{2}$/;
    return regex.test(value);
}

$(function() {
    // Datepicker 초기화
    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",  // 년-월-일 형식
        showMonthAfterYear: false,  // 년도가 앞에 오도록 설정
        changeYear: true,  // 년도 선택 가능하게
        changeMonth: true,  // 월 선택 가능하게
        yearSuffix: " ",  // 년도 뒤에 붙는 텍스트를 공백으로 설정
        monthNamesShort: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],  // 월을 숫자로 설정
        minDate: new Date(),  // 오늘 날짜부터 선택 가능
        maxDate: "+1Y",  // 1년 후까지 선택 가능

        onClose: function(dateText, inst) {
            if (!validateDateFormat(dateText)) {
                alert("날짜 형식이 올바르지 않습니다. YYYY-MM-DD 형식을 사용해 주세요.");
                $(this).val('');  // 잘못된 형식일 경우 입력값을 초기화
            }
        }
    });

    // 사용자가 직접 입력할 때도 날짜 형식을 검증
    $('#reservationForm').on('input', '.datepicker', function () {
        var value = $(this).val();
        if (!validateDateFormat(value)) {
            alert("날짜 형식이 올바르지 않습니다. YYYY-MM-DD 형식을 사용해 주세요.");
            $(this).val('');
        }
    });
   
});
//시간
$(function() {
    $('#reservationForm').on('submit', function(e) {
        // 선택된 시간과 분 값 가져오기
        let hour = $('#hour').val();
        let minute = $('#minute').val();

        // 시간과 분을 결합하여 하나의 시간 값으로 처리
        let timeValue = hour + ':' + minute;

        // time 필드를 hidden으로 만들어서 전송
        $('<input>').attr({
            type: 'hidden',
            id: 'time',
            name: 'time',
            value: timeValue
        }).appendTo('#reservationForm');
    });
});



    // 마이페이지 정보 수정 폼 처리
    $('.profile-form').submit(function (e) {
        const email = $('#e_mail').val().trim();
        const phoneNumber = $('#phone_number').val().trim();

        if (!validateEmail(email)) {
            alert('이메일 형식이 올바르지 않습니다.');
            e.preventDefault(); // 폼 제출 중지
            return;
        }

        if (!validatePhoneNumber(phoneNumber)) {
            alert('전화번호는 10자리 또는 11자리 숫자만 입력해 주세요.');
            e.preventDefault(); // 폼 제출 중지
            return;
        }
    });

    // 예약 수정 폼 처리
    $('#updateReservationForm').submit(function (e) {
        e.preventDefault(); // 폼 제출 기본 동작 방지
        var formData = $(this).serialize();

        $.ajax({
            url: 'update_reservation.php', // 예약 정보를 수정할 PHP 파일
            type: 'POST',
            data: formData,
            success: function (response) {
                alert('예약 정보가 성공적으로 수정되었습니다.');
                $('#consultantModal').modal('hide'); // 모달 닫기
                $('#myEventsContainer').load(window.location.href + ' #myEventsContainer > *');
            },
            error: function (xhr, status, error) {
                alert('예약 정보 수정 중 오류가 발생했습니다: ' + error);
            }
        });
    });

    // 이벤트 신청 폼 처리
    $('#reservationForm').submit(function(e) {
        e.preventDefault(); // 폼 제출 기본 동작 방지

        var formData = $(this).serialize();

        $.ajax({
            url: 'submit_event.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('이벤트가 성공적으로 신청되었습니다.');
                    $('#applyEventContainer').load(window.location.href + " #applyEventContainer > *");
                } else {
                    alert('이벤트 신청 중 오류가 발생했습니다: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX 요청 실패:', xhr.responseText); // 서버의 응답을 콘솔에 출력하여 디버깅
                alert("AJAX 요청 실패: " + error);
            }
        });
    });
});
// 날짜 순서 스크립트
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#myEventsContainer tbody');
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    rows.sort((rowA, rowB) => {
        const dateA = new Date(rowA.querySelector('td:nth-child(2)').textContent);
        const dateB = new Date(rowB.querySelector('td:nth-child(2)').textContent);
        return dateB - dateA;
    });

    // 정렬된 순서대로 다시 추가
    rows.forEach(row => tableBody.appendChild(row));
});


        const cities = {
            "KOR": ["서울A (강북구 도봉구 노원구)", "서울B (은평구 서대문구)", "서울C (중랑구 동대문구)", "서울D (강서구 양천구)", "서울E (영등포구 관악구)", "서울F (서초구 강남구)", "부산A (부산진구 강서구 사하구)", "부산B (수영구 해운대 동래구)", "인천A (강화군 연수구)", "인천B (부평구 계양구)", "대구A (김천시 달서구 고령군)", "대구B (수성구 경산시)", "대전A (유성구 대덕구 동.중.서구)", "광주A (광주)", "충북세종A (청주시 세종시 충주시)", "경기A (파주시 연천군 동두천)", "경기B (가평군 포천시 남양주시)", "경기C (김포시 부천시 시흥시)", "경기D (광명시 안양시 안산시)", "경기E (과천시 성남시 광주시)", "경기F (군포시 수원시 의왕시)", "경기G (화성시 오산시 평택시)", "경기H (용인시 안성시 여주시)", "강원A (강원)", "충남A (태안군 당진군 아산시)", "전북A (전북)", "전남A (전남)", "경북A (문경시 상주시 안동시)", "경북B (경주시 포항시 영천시)", "경남A (합천군 진주시 사천시)", "경남B (김해시 거제시 창원시)", "제주A (제주)"]
           , "AUS": ["브리즈번", "시드니"]
        
        };

        const cityValues = {
            "서울A (강북구 도봉구 노원구)": "SUA",
            "서울B (은평구 서대문구)": "SUB",
            "서울C (중랑구 동대문구)": "SUC",
            "서울D (강서구 양천구)": "SUD",
            "서울E (영등포구 관악구)": "SUE",
            "서울F (서초구 강남구)": "SUF",
            "부산A (부산진구 강서구 사하구)": "BSA",
            "부산B (수영구 해운대 동래구)": "BSB",
            "인천A (강화군 연수구)": "ICA",
            "인천B (부평구 계양구)": "ICB",
            "대구A (김천시 달서구 고령군)": "DGA",
            "대구B (수성구 경산시)": "DGB",
            "대전A (유성구 대덕구 동.중.서구)": "DJA",
            "광주A (광주)": "GJA",
            "충북세종A (청주시 세종시 충주시)": "CSA",
            "경기A (파주시 연천군 동두천)": "GGA",
            "경기B (가평군 포천시 남양주시)": "GGB",
            "경기C (김포시 부천시 시흥시)": "GGC",
            "경기D (광명시 안양시 안산시)": "GGD",
            "경기E (과천시 성남시 광주시)": "GGE",
            "경기F (군포시 수원시 의왕시)": "GGF",
            "경기G (화성시 오산시 평택시)": "GGG",
            "경기H (용인시 안성시 여주시)": "GGH",
            "강원A (강원)": "GWA",
            "충남A (태안군 당진군 아산시)": "CNA",
            "전북A (전북)": "JBA",
            "전남A (전남)": "JNA",
            "경북A (문경시 상주시 안동시)": "GUA",
            "경북B (경주시 포항시 영천시)": "GUB",
            "경남A (합천군 진주시 사천시)": "GNA",
            "경남B (김해시 거제시 창원시)": "GNB",
            "제주A (제주)": "JJA"
        };

        const districts = {
            "서울A (강북구 도봉구 노원구)": ["01 (도봉1,2동/방학1,2,3동/쌍문1동)", "02 (쌍문3,4동)", "03 (우이동)", "04 (미아동)", "05 (상계1~10동)", "06 (상계6,7동)", "07 (노원구)", "08 (정릉1,2,3,4동)", "09 (길음2동)", "10 (성북구)"],
            "서울B (은평구 서대문구)": ["01 (진관/갈현1/갈현2동)", "02 (불광1/불광2/응암1동)", "03 (역촌/신사1/신사2동)", "04 (북가좌1,2동/남가좌1,2동)", "05 (홍은1~3동/홍제1동)", "06 (망원1,2동/연남동/서교동)", "07 (공덕/대흥/아현동)", "08 (평창/부암동)", "09 (소공/희현동)", "10 (한강로동/용산2가동)"],
            "서울C (중랑구 동대문구)": ["01 (신내1,2동/묵1,2동)", "02 (면목2동/망우3동)", "03 (전농1동/답십리1동)", "04 (용산동/제기동)", "05 (마장동/사근동)", "06 (금호4가동/옥수동)", "07 (중곡1~4동/능동)", "08 (자양4동/군자동)", "09 (암사동/강일동)", "10 (천호2동/성내1동)", "11 (길동/둔촌1~2동)"],
            "서울D (강서구 양천구)": ["01 (신내1,2동/묵1,2동)", "02 (면목2동/망우3동)", "03 (발산1동/우장산동)", "04 (등촌3동/염창동)", "05 (산월1~5동)", "06 (신정1동)", "07 (목2동/목5동)", "08 (수궁동/항동)", "09 (개봉1동/고척2동)", "10 (신도림동/구로5동)"],
            "서울E (영등포구 관악구)": ["01 (양평1,2동/당산1,2동)", "02 (문래/영등포본동)", "03 (노량진1,2동)", "04 (신대방1,2동/상도1,3,4동)", "05 (흑석동/사당2동)", "06 (가산동/독산1동/시흥1동)", "07 (관악구 조원/신사/미성)", "08 (보라매,신림/성현/은천)", "09 (대학동/서림동)"],
            "서울F (서초구 강남구)": ["01 (반포본동/잠원동)", "02 (방배1,2,3,4동)", "03 (서초1,2동/내곡동)", "04 (압구정/신사동)", "05 (역삼1,2동/도곡1,2동)", "06 (대곡1,4동/대치2동)", "07 (수서동/세곡동)", "08 (풍남1,2동/잠실2,3,4,6동)", "09 (잠실본동/잠실7동)", "10 (오륜동/방이1,2동)", "11 (위례동/가락본동)"],
            "부산A (부산진구 강서구 사하구)": ["01 (가락동/강동동)", "02 (금곡동/화명1~3동)", "03 (덕천1~3동/구포1~3동)", "04 (모라1,3동/덕포1~2동)", "05 (충무동/남부민1~2동)", "06 (당리동/괴정1~4동)", "07 (장림1~2동/다대1~2동)", "08 (남포동/부평동/청학1~2동)", "09 (연지동/부전1동/부암1,3동)", "10 (가야1~2동/범천1~2동)"],
            "부산B (수영구 해운대 동래구)": ["01 (기장군)", "02 (남산동/구서1~2동/장전1~2동)", "03 (온천1~3동/명륜동)", "04 (수민동/사직1~3동)", "05 (거제1~4동/연산1~6동)", "06 (반송1~2동/반여1~4동)", "07 (우2,3동/재송1~2동)", "08 (좌1~3동/중1~2동)", "09 (광안1~4동/수영동)", "10 (부산동구/남구)", "11 (용호1~4동/용당동)"],
            "인천A (강화군 연수구)": ["01 (강화/옹진/불로대곡동)", "02 (마전/원단/아라동)", "03 (연희/청라1~3동)", "04 (가정1~2동/가좌1동)", "05 (중구/동구)", "06 (미추홀구 도화1~3동)", "07 (미추홀구 용현1~5동)", "08 (연수구 옥련1,2동)", "09 (연수구 청학동)", "10 (송도1~4동)"],
            "인천B (부평구 계양구)": ["01 (계양1~3동)", "02 (계산3동)", "03 (연희/청라1~3동)", "04 (가정1~2동/가좌1동)", "05 (중구/동구)", "06 (미추홀구 도화1~3동)", "07 (미추홀구 용현1~5동)", "08 (연수구 옥련1,2동)", "09 (연수구 청학동)"],
            "대구A (김천시 달서구 고령군)": ["01 (내당1~4동/원대동)", "02 (상중이동/다사읍)", "03 (옥포읍/논공읍)", "04 (신당동/이곡1~2동)", "05 (월성1~2동/진천동)", "06 (도원동/상인1,3동)", "07 (남산1~4동/성당동)", "08 (대구 남구)", "09 (김천)", "10 (칠곡/성주/고령)"],
            "대구B (수성구 경산시)": ["01 (북구 읍내동/국우동)", "02 (북구 구암동/관문동)", "03 (칠성동/대현동)", "04 (공산동/도평동)", "05 (해안동/지저동)", "06 (만촌1~3동/방촌동)", "07 (수성1~4가동/두산동)", "08 (수성구 지산1,2동/고산1~3동)", "09 (경산 와촌면/하양,진량읍)", "10 (경산 동부동/남산면)"],
            "대전A (유성구 대덕구 동.중.서구)": ["01 (유성 노은1동/진잠동)", "02 (유성 구즉/신성/온천2동)", "03 (덕암동/회덕동/송촌동)", "04 (서구 월평2,3동/만년/유성구 관편)", "05 (서구 갈마1~2동/월평1동)", "06 (서구 탄방/과정/용문동)", "07 (서구 관저1,2동/가수원동)", "08 (중구 문화1동/중촌동)", "09 (중구 문화2동/대흥/문창동)", "10 (동구 가양1~2동/용운동)"],
            "광주A (광주)": ["01 (광산구 송정1~2동/우산동)", "02 (광산구 첨단1,2동/신창/비아동)", "03 (광산구 수완/신가/운남동)", "04 (북구 건국/양산/일곡/매곡동)", "05 (북구 동림/운암1,2,3동)", "06 (북구 문흥동/두암동/중흥동)", "07 (서구 동천/유덕/치평/상무1,2동)", "08 (서구 금호동/화정동/풍암동)", "09 (남구 대촌/효덕/주월동)", "10 (남구 학동/지원동/학운동)"],
            "충북세종A (청주시 세종시 충주시)": ["01 (충주 살미면/웅양면/동량면)", "02 (충주/앙성/소태/엄정면)", "03 (제천/단양)", "04 (진천/증평/청주 청원구 오창읍)", "05 (청주 청원구 내수읍/북이면)", "06 (오송읍/옥산면/강내면)", "07 (청주 흥덕구 봉명1,2동)", "08 (청주 서원구 남이면/현도면)", "09 (청주시 가덕면/낭성면)", "10 (보은/옥천/영동)", "11 (세종 소정/전의/전동면)", "12 (세종 소담동/금남면)"],
            "경기A (파주시 연천군 동두천)": ["01 (신서면/중면)", "02 (파평면/문산읍)", "03 (조리읍/교하동)", "04 (운정2,3동)", "05 (송산동/송포동)", "06 (주엽1,2동/일산1~3동)", "07 (중산동/식사동)", "08 (장항1동/백석1~2동)", "09 (고양동/관산동)", "10 (능곡동/행주동)", "11 (흥도동/창릉동)"],
            "경기B (가평군 포천시 남양주시)": ["01 (포천)", "02 (양주시)", "03 (의정부 자금동/녹양동)", "04 (의정부 호원1,2동)", "05 (송산1~3동/신곡2동)", "06 (가평/양평)", "07 (남양주 진접/오납읍/수동면)", "08 (남양주 호평동/화도읍)", "09 (별내면/별내동/진건읍)", "10 (양정동/와부읍/조안면)", "11 (인창동/수택1~3동/갈매동)"],
            "경기C (김포시 부천시 시흥시)": ["01 (월곶면/대곶면)", "02 (운양동/장기동)", "03 (김포본동/고촌읍)", "04 (오정동/성곡동)", "05 (부천동/심곡동)", "06 (신중동/중동)", "07 (상동/대산동)", "08 (소사본동/범안동)", "09 (대야동/은행동/매화동)", "10 (신현동/군자동/신천동)", "11 (배곧동/정왕1~3동)"],
            "경기D (광명시 안양시 안산시)": ["01 (철산3동/광명7동)", "02 (학온동/소하2동)", "03 (석수1~3동/안양9동)", "04 (안양2동/비산1동)", "05 (비산3동/관양2동)", "06 (평안동/갈산동)", "07 (신길동/선부2동)", "08 (초지동/대부동)", "09 (월피동/안산동/반월동)", "10 (해양동/사동/사이동)"],
            "경기E (과천시 성남시 광주시)": ["01 (과천/성남 수정구 신촌)", "02 (성남 수정구/성남 중원구)", "03 (도촌동/은행2동)", "04 (운중동/삼평동)", "05 (야탑3동/서현1동)", "06 (구미1동/분당동)", "07 (천현동/미사1동)", "08 (춘궁동/감일동)", "09 (오포읍/곤지암읍/도척면)", "10 (퇴촌면/남종면)"],
            "경기F (군포시 수원시 의왕시)": ["01 (군포 산본1,2동/궁내)", "02 (군포1~2동)", "03 (청계동/오전동)", "04 (연무동/파장동)", "05 (율촌동/정자2~3동)", "06 (인계동/고등동)", "07 (입북동/금곡동)", "08 (서둔동/평동)", "09 (권선2동/매탄3동)", "10 (광교1동/원천동)", "11 (망포1~2동)"],
            "경기G (화성시 오산시 평택시)": ["01 (화성 송산/서신면)", "02 (화성 새솔동/남양읍)", "03 (정남면/화산동)", "04 (동탄1동/동탄2동)", "05 (동탄4동/동탄7동)", "06 (동탄6동/동탄8동)", "07 (세마동/중앙동)", "08 (안중읍/팽성읍)", "09 (진위면/고덕면)", "10 (비전1동/신편동)"],
            "경기H (용인시 안성시 여주시)": ["01 (용인 수지구 동천/신봉동)", "02 (용인 수지구 풍덕천1,2동)", "03 (용인 수지구 죽전1동)", "04 (마북동/구성동)", "05 (상갈동/기흥동)", "06 (역삼동/이동읍)", "07 (포곡읍/양지면)", "08 (금광면/죽산면)", "09 (마장면/율면)", "10 (대신면/점동면)"],
            "강원A (강원)": ["01 (철원/화천/양구)", "02 (춘천/사북/신북/북산)", "03 (춘천 남산면/신동면)", "04 (속초/양양/강릉 주문진)", "05 (강릉 구정면/강동면/옥계면)", "06 (홍천/횡성/평창)", "07 (원주 호저/소초/지정)", "08 (원주 문막읍/흥업면/귀래면)", "09 (원주 단구/판부/신림)", "10 (동해/삼척)"],
            "충남A (태안군 당진군 아산시)": ["01 (태안군/당진군/아산시)", "02 (서산시/홍성군)", "03 (천안시/아산시)", "04 (공주시/부여군)", "05 (논산시/계룡시)", "06 (금산군/부여군)", "07 (서천군/청양군)", "08 (예산군/홍성군)", "09 (당진군/서산시)", "10 (아산시/천안시)", "11 (논산시/계룡시)", "12 (부여군/청양군)", "13 (서천군/청양군)"],
            "전북A (전북)": ["01 (전주 덕진구 조촌/여의동)", "02 (전주 덕진구 우아1~2동/진북동)", "03 (전주 완산구 서신/중화1,2동/중앙동)", "04 (전주 완산구 평화1~2동)", "05 (군산 옥도/소룡/미성/옥서)", "06 (군산 대야면/개정면/화현면)", "07 (익산 용안면/낭산면/삼기면)", "08 (익산 모현/남종/영등1동)", "09 (김제/부안)", "10 (정읍/고창)", "11 (완주/진안/무주)", "12 (임실/장수/순창/남원)"],
            "전남A (전남)": ["01 (장성/영광/함평)", "02 (담양/화순/곡성/구례)", "03 (나주/영암)", "04 (무안/신안/목포 삼향)", "05 (목포 신흥동/유달동/목원동)", "06 (해남/진도/완도)", "07 (강진/보성/고흥/장흥)", "08 (순천 해룡/덕연/도시/별량)", "09 (순천 서면/삼산동/승주읍)", "10 (여수 주삼/여천/쌍봉/시전)", "11 (여수 여서동/충무동/돌산읍)", "12 (광양)"],
            "경북A (문경시 상주시 안동시)": ["01 (문경읍/상주)", "02 (영주시/예천읍)", "03 (안동)", "04 (봉화읍/울진/영양/영덕)", "05 (옥성/도개/무을/선산)", "06 (구미 선주원남/도량)", "07 (구미 양포동/인동동)", "08 (포항 북구 송라/죽장/청하)", "09 (포항 남구 대이/송도)", "10 (포항 남구 청림동/장기면)"],
            "경북B (경주시 포항시 영천시)": ["01 (의성읍/금성면/봉양면)", "02 (산내면/운문면/금천면)", "03 (경주 불국동/보덕동/천북면)", "04 (울산 울주군)", "05 (울산 울주군 범서읍/북구 농소3동)", "06 (울산 북구 강동동/송정동)", "07 (울산 중구 약사동/성안동)", "08 (울산 남구 삼호동/신정1~4동)", "09 (울산 남구 옥동/선암동)", "10 (울산 동구남목1~3동/대송동)"],
            "경남A (합천군 진주시 사천시)": ["01 (의성읍/금성면/거창면/함양/합천)", "02 (창녕/의령/함안)", "03 (밀양/양산 원동/하북)", "04 (밀양 평산동/삼성동/중앙동)", "05 (밀양 양주/물금)", "06 (하동/남해/진주 수곡)", "07 (진주 미천,명석/집현/대곡)", "08 (진주 문산읍/금곡면)", "09 (사천/고성)", "10 (통영/거제 둔덕)", "11 (거제 연초면/하청면)"],
            "경남B (김해시 거제시 창원시)": ["01 (창원 마산합포구)", "02 (창원 마산회원구)", "03 (창원 마산회원구 구암1,2동)", "04 (창원 의창구 팔룡동)", "05 (창원 성산구 반송/중앙)", "06 (창원 진해구 여좌/태백)", "07 (창원 진해구 웅천동)", "08 (김해 진영/한림/진례)", "09 (김해 생림/상동/북부)", "10 (김해 장유1~3동)"],
            "제주A (제주)": ["01 (제주시 한경/한림/애월/외동동)", "02 (제주시 연동/오라/용담1동)", "03 (제주시 봉개동/조천읍)", "04 (서귀포 전체)"]
        };

        $(document).ready(function () {
            let citySelect = $('#city');
            let districtSelect = $('#district');
            
            // 페이지 로드 시 초기 설정
            function initializeSelections() {
                let country = $('#country').val();
        
                citySelect.empty().append('<option value="">MC 선택</option>');
                districtSelect.empty().append('<option value="">CC 선택</option>').prop('disabled', true);
        
                if (country && cities[country]) {
                    cities[country].forEach(function (city) {
                        citySelect.append(new Option(city, cityValues[city]));
                    });
                    citySelect.prop('disabled', false);
                } else {
                    citySelect.prop('disabled', true);
                }
            }
        
            // 국가 선택 시 도시 목록 업데이트
            $('#country').change(function () {
                initializeSelections();
            });
        
            // 도시 선택 시 구역 목록 업데이트
            $('#city').change(function () {
                let city = $(this).val();
                let cityName = Object.keys(cityValues).find(key => cityValues[key] === city);
        
                districtSelect.empty().append('<option value="">CC 선택</option>');
        
                if (cityName && districts[cityName]) {
                    districts[cityName].forEach(function (district) {
                        let [code, name] = district.split(' ');
                        districtSelect.append(new Option(district, code));
                    });
                    districtSelect.prop('disabled', false);
                } else {
                    districtSelect.prop('disabled', true);
                }
            });
        
            // 페이지 로드 시 초기 설정 호출
            initializeSelections();
        });
        
        