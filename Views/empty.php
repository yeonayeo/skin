<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container">
    <div class="page_head">
      <h2 class="page_title">고객 정보</h2>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {}
        }
      },
      mounted() {
        sunrise({
          data: {},
          target: '/client/popup_password'
        })
      },
      methods: {}
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
