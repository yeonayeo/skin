<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">문자 발송</h3>
    </div>
    <div class="page_contents">
      <div class="noti_area">
        ※ 모든 문자는 매장 번호 (031-1111-1111)로 발송되며, <span class="c_red">발송시 취소가 불가능</span>하니 수신인을 정확하게 확인한 후 발송해주세요.<br><br>
        자동 변환 문구 안내<br>
        1. 고객명 : 고객의 이름으로 자동 변환 <span class="c_grey">(ex. 고객명님 = 김미연님)</span><br>
        2. N일 : 방문일, N시 : 방문시간 자동 변환 <span class="c_grey">(ex. N일 N시 방문입니다. = 1월 1일 오전 10시 방문입니다.)</span><br>
        ※ 단, 즉시 발송시 적용되지 않으니 <span class="c_red">반드시 예약 발송에만 사용</span>해주세요.
      </div>
      <ul class="sms_list">
        <li v-for="(template, index) in list">
          <div class="sms">
            <div class="title">
              <input type="text" placeholder="제목을 입력하세요" v-model="template.title" @keyup="save_title($event, template.id)">
            </div>
            <div class="content">
              <textarea placeholder="내용을 입력하세요" v-model="template.msg" @keyup="save_msg($event, template.id)"></textarea>
            </div>
          </div>
          <div class="btn_box mt10" v-if="template.is_bottom">
            <button type="button" class="btn c4 s" style="width: 100px;" @click="popup_booking_send(template.id)">예약 발송</button>
            <button type="button" class="btn e4 s" style="width: 100px;" @click="popup_now_send(template.id)">즉시 발송</button>
          </div>
        </li>
      </ul>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {},
          list: RES.list,
          title_timeout: null,
          msg_timeout: null
        }
      },
      mounted() {},
      methods: {
        popup_booking_send(id) {
          popup_booking_send = sunrise({
            data: {},
            target: '/setting/sms/popup_booking_send?id='+id
          })
        },
        popup_now_send(id) {
          popup_now_send = sunrise({
            data: {},
            target: '/setting/sms/popup_now_send?id='+id
          })
        },
        save_title(e, template_id) {
          clearTimeout(this.title_timeout);
            this.title_timeout = setTimeout(async () => {
              let req = {
              id: template_id,
              type: 'title',
              title: e.target.value
            };

            $.ajax({
              url: '/setting/sms/action_update',
              data: req,
              success: (res) => {
                if (res.res_cd !== 'OK') {
                  console.log(res);
                }
              }
            });
          }, 500);
        },
        save_msg(e, template_id) {
          clearTimeout(this.msg_timeout);
            this.msg_timeout = setTimeout(async () => {
              let req = {
              id: template_id,
              type: 'msg',
              msg: e.target.value
            };
            $.ajax({
              url: '/setting/sms/action_update',
              data: req,
              success: (res) => {
                if (res.res_cd !== 'OK') {
                  console.log(res);
                }
              }
            });
          }, 500);
        },
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
