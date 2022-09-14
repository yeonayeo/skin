<? require_once VIEWS_PATH.'/_include/head.php'; ?>

<div id="login_wrap">
  <div class="logo_area">
    <h1 class="logo"><img src="/assets/images/common/logo_login.svg" alt=""></h1>
    <h2 class="logo_txt">고객 관리 시스템</h2>
  </div>
  <div class="input_area" id="login">
    <input type="text" placeholder="로그인 코드를 입력하세요" @keypress.enter="action_login" v-model="login_cd" ref="login_cd">
    <button type="button" class="btn c1 m" @click="action_login">로그인</button>
  </div>
  <div class="bottom_area">
    <div class="contact">비상 연락 : 010-9048-4431</div>
  </div>

  <script>
    var RES = <?=json_encode($_RES);?>;

    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {},
          login_cd : null
        }
      },
      mounted() {},
      methods: {
        action_login: function(e) {
          if (!this.login_cd) {
            alert('로그인 코드를 입력하세요.');
            this.$refs.login_cd.focus();
            return;
          }

          $.ajax({
            url: '/auth/action_login',
            data: {
              login_cd: this.login_cd
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = '/';
              } else {
                alert(res.err_msg);
              }
            }
          });
        }
      }
    });

    FRONT.mount('#login');
  </script>
</div>

<? require_once VIEWS_PATH.'/_include/foot.php'; ?>
