function fn_press_han(obj) {
    //좌우 방향키, 백스페이스, 딜리트, 탭키에 대한 예외
    if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 ||
        event.keyCode == 46) return;
    obj.value = obj.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]|[~!@#$%^&*()-_=+|\;:'"{},<.>/`?()]/g, '');
}

function removeChar(event) {
    event = event || window.event;
    var keyID = (event.which) ? event.which : event.keyCode;
    if (keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39)
        return;
    else
        event.target.value = event.target.value.replace(/[^0-9]/g, "");
}

function check_sch() {
    var register = $('.register');
    var adrChecked = $('.adrChecked');
    adrChecked.val("");
    register.attr("disabled", true);
    var popupX = (document.body.offsetWidth / 2) - (850 / 2);
    var popupY = (window.screen.height / 2) - (500 / 2);

    url = "sch_adr.php";
    window.open(url, '', 'status=no, height=500, width=850, left=' + popupX + ', top=' + popupY + ', screenX=' + popupX + ', screenY= ' + popupY);
}

function sel_sch(data) {
    var memberId = $('.memberId');
    var memberIdCheck = $('.memberIdCheck');
    var checkAlertId = $('.checkAlertId');
    var memberPw = $('.pw');
    var memberPw2 = $('.pwCheck');
    var checkAlertPw = $('.checkAlertPw');
    var idChecked = $('.idChecked');
    var pwChecked = $('.pwChecked');
    var idChecked2 = false;
    var name = $('.name');
    var nameChecked = $('.nameChecked');
    var memberPh = $('.ph');
    var phChecked = $('.phChecked');
    var memberAdr = $('#country');
    var adrChecked = $('.adrChecked');
    var email = $('#email');
    var str_email02 = $('#str_email02');
    var emailChecked = $('.emailChecked');
    var register = $('.register');
    if (data === "self") {
        $("input[name='country']").attr("readonly", false);
        memberAdr.on("propertychange change keyup paste input", function() {
            register.attr("disabled", true);
            if (memberAdr.val() != "") {
                adrChecked.val("1");
                if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                    register.attr("disabled", false);
                }
            } else {
                register.attr("disabled", false);
            }
        });
    } else {
        $(document).ready(function() {
            $('#country').val(data);
            adrChecked.val("1");
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                register.attr("disabled", false);
            } else {
                register.attr("disabled", false);
            }

            $("input[name='country']").attr("readonly", true);
        });
    }
}

$(function() {

    var memberId = $('.memberId');
    var memberIdCheck = $('.memberIdCheck');
    var checkAlertId = $('.checkAlertId');
    var memberPw = $('.pw');
    var memberPw2 = $('.pwCheck');
    var checkAlertPw = $('.checkAlertPw');
    var idChecked = $('.idChecked');
    var pwChecked = $('.pwChecked');
    var idChecked2 = false;
    var name = $('.name');
    var nameChecked = $('.nameChecked');
    var memberPh = $('.ph');
    var phChecked = $('.phChecked');
    var memberAdr = $('#country');
    var adrChecked = $('.adrChecked');
    var email = $('#email');
    var str_email02 = $('#str_email02');
    var emailChecked = $('.emailChecked');
    var register = $('.register');

    // 직책 라디오 체크
    $(document).ready(function() {
        // 라디오 버튼에 대한 이벤트 리스너 설정
        $("input[name='position']").change(function() {
            var selectedValue = $(this).val(); // 선택된 라디오 버튼의 값 가져오기

            // 선택된 값에 따라 직책 필드 업데이트
            if (selectedValue === '4') {
                $('#job').val('지문분석전문가'); // 지문분석전문가 선택 시
            } else if (selectedValue === '5') {
                $('#job').val('컨설턴트'); // 컨설턴트 선택 시
            }
        });
    });

// 아이디 입력 체크
    memberId.on("propertychange change keyup paste input", function() {
        idChecked.val("");
        checkAlertId.empty();
        register.attr("disabled", true);
        memberIdCheck.attr("disabled", true);
        if (memberId.val().length >= 4 && memberId.val().length <= 12) {
            checkAlertId.empty();
            memberIdCheck.attr("disabled", false);
            checkAlertId.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">중복확인을 눌러주세요</td>');
            // 아이디 중복 체크
            // 동적 버튼 추가후 click 이벤트 중복 에러
            // 아래처럼 하면 중복 호출됨
            // 중복 호출 방지    
            memberIdCheck.off('click').on('click', function() {
                idChecked.val("");
                idChecked2 = true;
                // 아이디 값 체크
                // console.log(memberId.val());

                $.ajax({
                    type: 'post',
                    dataType: 'HTML',
                    url: 'checkID.php',
                    data: {
                        memberId: memberId.val()
                    },

                    success: function(json) {
                        console.log(json);
                        if (json == '{"res":"good"}') {
                            idChecked2 = false;
                            // 호출 횟수 체크
                            console.count('count');
                            console.log(json);
                            checkAlertId.empty();
                            checkAlertId.append('<td class="alert alert-success" id="alert-success" colspan=3 ">사용가능한 아이디입니다.</td>');
                            memberIdCheck.attr("disabled", true);
                            idChecked.val('1');
                            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && adrChecked.val() == '1' && emailChecked.val() == '1') {
                                register.attr("disabled", false);
                            }

                        } else {
                            memberIdCheck.attr("disabled", true);
                            idChecked2 = false;
                            idChecked.val("");
                            checkAlertId.empty();
                            checkAlertId.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">다른 아이디를 입력해 주세요.</td>');
                            memberId.val("");
                            memberId.focus();
                        }
                    },

                    error: function() {
                        console.log('failed');
                    }
                })
            });
        } else {
            checkAlertId.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">아이디는 4~12자의 영문 대소문자와 숫자로만 입력</td>');
            memberId.focus();
        }

    });

    // 비밀번호 체크
    memberPw.on("propertychange change keyup paste input", function() {
        register.attr("disabled", true);
        checkAlertPw.empty();
        if (memberPw.val().length >= 8 && memberPw.val().length <= 12) {
            checkAlertPw.empty();
            if (memberPw.val() == memberPw2.val()) {
                checkAlertPw.empty();
                checkAlertPw.append('<td class="alert alert-success" id="alert-success" colspan=3 ">비밀번호가 일치합니다.</td>');
                pwChecked.val('1');
                if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                    register.attr("disabled", false);
                }
            } else {
                checkAlertPw.empty();
                pwChecked.val("");
                checkAlertPw.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호를 확인해주세요.</td>');
                memberPw2.on("propertychange change keyup paste input", function() {
                    register.attr("disabled", true);
                    checkAlertPw.empty();
                    pwChecked.val("");
                    if (memberPw.val() == memberPw2.val()) {
                        checkAlertPw.empty();
                        checkAlertPw.append('<td class="alert alert-success" id="alert-success" colspan=3 ">비밀번호가 일치합니다.</td>');
                        pwChecked.val('1');
                        if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                            register.attr("disabled", false);
                        }
                    } else {
                        checkAlertPw.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호가 일치하지 않습니다.</td>');
                        memberPw2.focus();
                        pwChecked.val("");
                    }
                });
            }
        } else {
            checkAlertPw.append('<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호는 8~12자의 영문 대소문자와 숫자로만 입력</td>');
            memberPw.focus();
        }
    });

    // 이름 입력 체크
    name.on("propertychange change keyup paste input", function() {
        register.attr("disabled", true);
        if (name.val() != "") {
            nameChecked.val("1");
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                register.attr("disabled", false);
            }
        } else {
            register.attr("disabled", true);
        }
    });

    // 연락처 입력 체크
    memberPh.on("propertychange change keyup paste input", function() {
        register.attr("disabled", true);
        if (memberPh.val() != "") {
            phChecked.val("1");
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                register.attr("disabled", false);
            }
        } else {
            register.attr("disabled", true);
        }
    });

    //이메일 직접입력
    $('#selectEmail').change(function() {
        $("#selectEmail option:selected").each(function() {
            if ($(this).val() == '1') { //직접입력일 경우 
                $("#str_email02").val(''); //값 초기화 
                $("#str_email02").attr("disabled", false); //활성화 
            } else { //직접입력이 아닐경우 
                $("#str_email02").val($(this).text()); //선택값 입력 
                $("#str_email02").attr("disabled", true); //비활성화 
            }

        });
    });

    // 이메일 입력 체크
    email.on("propertychange change keyup paste input", function() {
        register.attr("disabled", true);
        if (name.val() != "") {
            emailChecked.val("1");
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked.val() == '1' && emailChecked.val() == '1') {
                register.attr("disabled", false);
            }
        } else {
            emailChecked.val("");
        }
    });

});