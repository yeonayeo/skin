<div id="popup_confirm_book" class="popup_wrap" style="width: 520px;" v-cloak>
  <div class="popup_contents">
    <div class="content mb40">
      <div class="notice mb20">완료 처리시 취소할 수 없습니다.</div>
      <textarea placeholder="관리시 특이사항이 있었다면 기록해주세요." v-model="special_note"></textarea>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_confirm">완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_confirm_book.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_CONFIRM_BOOK = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        id: POPUP_RES.id,
        special_note: null,
        ticket_list: POPUP_RES.ticket_list
      }
    },
    mounted() {},
    methods: {
      action_confirm() {
        let req = {
          id: this.id,
          special_note: this.special_note
        };

        $.ajax({
          url: '/schedule/action_confirm',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('관리 완료되었습니다.');
              location.href = '/';
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_CONFIRM_BOOK.mount('#popup_confirm_book');
</script>
