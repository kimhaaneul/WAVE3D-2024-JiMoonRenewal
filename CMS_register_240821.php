<script>
function fn_press_han(obj) {
    //좌우 방향키, 백스페이스, 딜리트, 탭키에 대한 예외
    if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 ||
        event.keyCode == 46) return;
    //obj.value = obj.value.replace(/[\a-zㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
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
    window.open(url, '', 'status=no, height=500, width=850, left=' + popupX + ', top=' + popupY + ', screenX=' +
        popupX + ', screenY= ' + popupY);
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
                if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked
                    .val() == '1' && emailChecked.val() == '1') {
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
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' && phChecked
            .val() == '1' && emailChecked.val() == '1') {
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
            checkAlertId.append(
                '<td class="alert alert-danger" id="alert-danger" colspan=3 ">중복확인을 눌러주세요</td>');
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
                            checkAlertId.append(
                                '<td class="alert alert-success" id="alert-success" colspan=3 ">사용가능한 아이디입니다.</td>'
                                );
                            memberIdCheck.attr("disabled", true);
                            idChecked.val('1');
                            if (idChecked.val() == '1' && pwChecked.val() == '1' &&
                                nameChecked.val() == '1' && phChecked.val() ==
                                '1' && adrChecked.val() == '1' && emailChecked
                            .val() == '1') {
                                register.attr("disabled", false);
                            }

                        } else {
                            memberIdCheck.attr("disabled", true);
                            idChecked2 = false;
                            idChecked.val("");
                            checkAlertId.empty();
                            checkAlertId.append(
                                '<td class="alert alert-danger" id="alert-danger" colspan=3 ">다른 아이디를 입력해 주세요.</td>'
                                );
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
            checkAlertId.append(
                '<td class="alert alert-danger" id="alert-danger" colspan=3 ">아이디는 4~12자의 영문 대소문자와 숫자로만 입력</td>'
                );
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
                checkAlertPw.append(
                    '<td class="alert alert-success" id="alert-success" colspan=3 ">비밀번호가 일치합니다.</td>'
                    );
                pwChecked.val('1');
                if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' &&
                    phChecked.val() == '1' && emailChecked.val() == '1') {
                    register.attr("disabled", false);
                }
            } else {
                checkAlertPw.empty();
                pwChecked.val("");
                checkAlertPw.append(
                    '<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호를 확인해주세요.</td>'
                    );
                memberPw2.on("propertychange change keyup paste input", function() {
                    register.attr("disabled", true);
                    checkAlertPw.empty();
                    pwChecked.val("");
                    if (memberPw.val() == memberPw2.val()) {
                        checkAlertPw.empty();
                        checkAlertPw.append(
                            '<td class="alert alert-success" id="alert-success" colspan=3 ">비밀번호가 일치합니다.</td>'
                            );
                        pwChecked.val('1');
                        if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked
                            .val() == '1' && phChecked.val() == '1' && emailChecked.val() ==
                            '1') {
                            register.attr("disabled", false);
                        }
                    } else {
                        checkAlertPw.append(
                            '<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호가 일치하지 않습니다.</td>'
                            );
                        memberPw2.focus();
                        pwChecked.val("");
                    }
                });
            }
        } else {
            checkAlertPw.append(
                '<td class="alert alert-danger" id="alert-danger" colspan=3 ">비밀번호는 8~12자의 영문 대소문자와 숫자로만 입력</td>'
                );
            memberPw.focus();
        }
    });

    // 이름 입력 체크
    name.on("propertychange change keyup paste input", function() {
        register.attr("disabled", true);
        if (name.val() != "") {
            nameChecked.val("1");
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' &&
                phChecked.val() == '1' && emailChecked.val() == '1') {
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
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' &&
                phChecked.val() == '1' && emailChecked.val() == '1') {
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
            if (idChecked.val() == '1' && pwChecked.val() == '1' && nameChecked.val() == '1' &&
                phChecked.val() == '1' && emailChecked.val() == '1') {
                register.attr("disabled", false);
            }
        } else {
            emailChecked.val("");
        }
    });

});
</script>

<div class="container-fluid">
    <div class="container">
        <div class="row">

            <span class="middle"></span> </br>
            <span class="content"></span>
        </div>
    </div>
</div>

<div class="container-fluid mt-5" style="text-align: center;">
    <h1 style="font-size : 2em; margin-top: 0; font-weight: bold;"><span style="color: #FF5222; font-weight: 900;">CAP
            AI지문솔루션</span> 회원가입</h1>
    <div class="container">
        <div class="row">
            <span class="capteaching"></span>　<span class="intro"></span></br>

            <span class="content"></span>
        </div>
    </div>
</div>
<div class="container-fluid">

    <div class="container" style="margin-top: 0em; margin-bottom: 2em">
        <form name="join" action="BNS_registerDB.php" method="post">
            <table class="table table-bordered" style="margin-bottom:0; border-top: solid red;">
                <tr style="display: table-row">
                    <th><span style="color:red">*</span>회원구분 : </th>
                    <td>
                        <div>
                            <label class="radio-inline"
                                style="margin:0; padding:0; margin-right:18px; margin-bottom:10px;">
                                <INPUT type="radio" class="rd-q0" value="5" name="position">
                                컨설턴트
                            </label>
                            <label class="radio-inline"
                                style="margin:0; padding:0; margin-right:18px; margin-bottom:10px;">
                                <INPUT type="radio" class="rd-q0" value="4" name="position">
                                지문분석전문가
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th style="width:20%;">
                        <span>*</span>
                        아이디:
                    </th>
                    <td colspan=3>
                        <input style="width:70%; float: none; margin: 0 auto;" type="text" name="id"
                            class="memberId form-control" placeholder="아이디는 4~12자의 영문 대소문자와 숫자로만 입력"
                            onkeyup='fn_press_han(this);' maxlength="12" required />
                        <input style="width:20%; float: right; margin: 0 auto; display:inline;border-radius: 15px;"
                            type="button" value="중복확인" class="memberIdCheck btn btn-outline-info" disabled />
                    </td>
            </table>
            <table class="table table-bordered" style="margin-top:0; margin-bottom:0;">
                <tr class="checkAlertId"></tr>
            </table>
            <table class="table table-bordered" style="margin-bottom:0;">
                <tr>
                    <th style="width:20%;">
                        <span>*</span>
                        비밀번호:
                    </th>
                    <td colspan=3>
                        <input style="width:80%;" type="password" name="pw" class="pw form-control"
                            placeholder="비밀번호는 8~12자의 영문 대소문자와 숫자로만 입력" maxlength="12" required />
                    </td>
                </tr>
                <tr>
                    <th>
                        <span>*</span>
                        비밀번호 확인:
                    </th>
                    <td colspan=3>
                        <input style="width:80%;" type="password" name="pwCheck" class="pwCheck form-control"
                            maxlength="12" required />
                    </td>
                </tr>
            </table>
            <table class="table table-bordered" style="margin-top:0; margin-bottom:0;">
                <tr class="checkAlertPw"></tr>
            </table>
            <table class="table table-bordered" style="margin-top:0; border-bottom: solid red;">
                <tr>
                    <th style="width:20%;">
                        <span>*</span>
                        이름:
                    </th>
                    <td colspan=3>
                        <input style="width:80%;" type="text" name="name" class="name form-control" required />
                    </td>
                </tr>
                <tr>
                    <th style="width:20%;">
                        <span>*</span>
                        주소:
                    </th>
                    <td colspan=3>
                        <input style="width:80%;" type="text" name="address" class="name form-control" required />
                    </td>
                </tr>
                <tr style="display: none;">
                    <th style="width:20%;">
                        <span>*</span>
                        직책:
                    </th>
                    <td colspan=3>
                        <input type="text" id="job" name="job" class="form-control" required />
                    </td>
                </tr>
                <tr>
                    <th style="width:20%;">
                        <span>*</span>
                        연락처 :
                    </th>
                    <td colspan=3>
                        <input type="text" placeholder="-제외 (숫자만)" onkeyup='removeChar(event)'
                            style='ime-mode:disabled;' name="ph" class="ph form-control check-need" maxlength="11"
                            required />
                    </td>
                </tr>
                <tr>
                    <th>
                        <span>*</span>
                        e-mail:
                    </th>
                    <td colspan=3>
                        <div class="row">
                            <input type="text" placeholder="" name="email" id="email"
                                style="width: 30%; float: none; margin: 0 auto;" class="form-control check-need"> @
                            <input type="text" name="str_email02" id="str_email02"
                                style="width: 30%; float: none; margin: 0 auto;" class="form-control check-need"
                                disabled="" value="naver.com">
                            <select style="width: 30%; float: none; margin: 0 auto;" class="form-control check-need"
                                name="selectEmail" id="selectEmail">
                                <option value="1">직접입력</option>
                                <option value="naver.com" selected="">naver.com</option>
                                <option value="hanmail.net">hanmail.net</option>
                                <option value="hotmail.com">hotmail.com</option>
                                <option value="nate.com">nate.com</option>
                                <option value="yahoo.co.kr">yahoo.co.kr</option>
                                <option value="empas.com">empas.com</option>
                                <option value="dreamwiz.com">dreamwiz.com</option>
                                <option value="freechal.com">freechal.com</option>
                                <option value="lycos.co.kr">lycos.co.kr</option>
                                <option value="korea.com">korea.com</option>
                                <option value="gmail.com">gmail.com</option>
                                <option value="hanmir.com">hanmir.com</option>
                                <option value="paran.com">paran.com</option>
                            </select>
                        </div>
                        <span class="my-3 text-muted" style="margin-top: 50px;">e-mail은 아이디와 비밀번호 찾기에 사용됩니다. 정확하게 입력해
                            주세요.</span>
                    </td>
                </tr>

            </table>

            <div class="text-center mt-3 mb-5">
                <input class="register btn btn-outline-info" type="submit" value="회원가입" disabled />
            </div>
            <div class="formCheck">
                <input type="hidden" name="idChecked" class="idChecked" />
                <input type="hidden" name="pwChecked" class="pwChecked" />
                <input type="hidden" name="nameChecked" class="nameChecked" />
                <input type="hidden" name="phChecked" class="phChecked" />
                <input type="hidden" name="adrChecked" class="adrChecked" />
                <input type="hidden" name="emailChecked" class="emailChecked" />

            </div>
    </div>

    </form>

    </body>

    </html>