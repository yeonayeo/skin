<div id="popup_password" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_password.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_contents">
    <div class="content mb60 mt40 ta_c">
      <div class="notice mb30">고객 정보를 열람하려면<br>비밀번호를 입력해주세요</div>
      <input type="password" placeholder="비밀번호를 입력하세요" @keypress.enter="action_password" style="width: 400px;" v-model="pw" ref="pw">
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_password">입력 완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" v-if="client_id" onclick="popup_password.sunrise('closePopup');">취소</button>
      <button type="button" class="btn e2 l" style="width: 110px;" v-if="!client_id" onclick="location.href = '/'">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_PASSWORD = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        pw: null,
        client_id: POPUP_RES.client_id
      }
    },
    mounted() {},
    methods: {
      action_password: function(e) {
        if (!this.pw) {
          alert('로그인 코드를 입력하세요.');
          this.$refs.pw.focus();
          return;
        }

        $.ajax({
          url: '/client/action_password',
          data: {
            pw: this.pw
          },
          success: (res) => {
            if (res.res_cd === 'OK') {
              if(this.client_id) {
                location.href = '/client/detail?id='+this.client_id;
              } else {
                location.href = '/client';
              }
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_PASSWORD.mount('#popup_password');
</script>
