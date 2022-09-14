<div id="popup_update_password" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_update_password.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">고객 정보 비밀번호</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">비밀번호 변경</div>
      </div>
      <div class="content_body">
        <table>
          <tbody>
            <tr>
              <th>현재 비밀번호</th>
              <td>
                <input type="password" placeholder="현재 비밀번호를 입력하세요" v-model="pw">
              </td>
            </tr>
            <tr>
              <th style="vertical-align: top; padding-top: 10px;">새 비밀번호</th>
              <td>
                <input type="password" placeholder="새 비밀번호를 입력하세요" v-model="new_pw" ref="new_pw">
                <input type="password" class="mt10" placeholder="새 비밀번호를 한 번 더 입력하세요" v-model="confirm_pw">
              </td>
            </tr>
          </tbody>
        </table>
        <div class="noti_txt">※ 비밀번호는 4~8자의 숫자 혹은 문자+숫자로 설정해주세요.</div>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update_password">수정 완료</button>
      <button type="button" class="btn e1 l" style="width: 110px;" onclick="popup_update_password.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_UPDATE_PASSWORD = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},
        pw: null,
        new_pw: null,
        confirm_pw: null
      }
    },
    mounted() {},
    methods: {
      action_update_password() {
        let req = {
          pw: this.pw,
          new_pw: this.new_pw
        }

        if (!req.pw) return alert('비밀번호를 입력하세요');
        if (!req.new_pw) return alert('새 비밀번호를 입력해주세요');
        if (req.new_pw != this.confirm_pw) return alert('새 비밀번호가 동일하지 않습니다.');
        if (req.pw == this.new_pw) return alert('현재 비밀번호와 다르게 입력해주세요.');

        if (!/[a-z]|[0-9]{4,8}$/.test(req.new_pw)) {
          alert('비밀번호 형식이 잘못되었습니다.');
          this.$refs.new_pw.focus();
          return;
        }

        $.ajax({
          url: '/setting/admin/action_update_password',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('비밀번호가 변경되었습니다.');
              location.href = `/setting/admin`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_UPDATE_PASSWORD.mount('#popup_update_password');
</script>
