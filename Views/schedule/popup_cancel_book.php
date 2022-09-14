<div id="popup_cancel_book" class="popup_wrap" style="width: 520px;" v-cloak>
  <div class="popup_contents">
    <div class="content mb40">
      <div class="notice mb20">예약 취소 종류를 선택해주세요.</div>
      <div class="radio_box mb20">
        <div class="row">
          <label class="radio">
            <input type="radio" value="1" v-model="cancel_type_cd">
            <span>일반 취소<em class="noti_g">(패널티 없음)</em></span>
          </label>
        </div>
        <div class="row mt20" v-if="ticket_id">
          <label class="radio">
            <input type="radio" value="2" v-model="cancel_type_cd">
            <span>당일 취소<em class="noti_r">(패널티 부여)</em></span>
          </label>
        </div>
      </div>
      <div class="penalty_box mb15" v-if="cancel_type_cd==2 && ticket_id">
        <div class="title">이용권 패널티</div>
        <div class="input_box">
          <select style="width: 220px;" v-model="ticket_id">
            <option value=null>이용권 미선택</option>
            <option v-for="(ticket, index) in ticket_list" :value="ticket.id">{{ ticket.name_txt }}</option>
          </select>
          <input type="number" placeholder="-" style="width: 60px; text-align: right;" v-model="ticket_number">
          <span>차감</span>
        </div>
      </div>
      <textarea placeholder="취소 사유를 작성해주세요." style="height: 80px;" v-model="cancel_reason"></textarea>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c3 l" style="width: 220px;" @click="action_cancel">예약 취소 완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_cancel_book.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_CANCEL_BOOK = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},
        id: POPUP_RES.id,
        manage_name: POPUP_RES.manage_name,
        booking_date: POPUP_RES.booking_date,
        cancel_reason: null,
        client_id: POPUP_RES.client_id,
        ticket_id: POPUP_RES.ticket_id,
        ticket_number: null,
        ticket_type_cd: POPUP_RES.ticket_type_cd,
        remain_number: POPUP_RES.remain_number,
        ticket_list: POPUP_RES.ticket_list,
        cancel_type_cd : 1
      }
    },
    mounted() {},
    methods: {
      action_cancel() {
        let req = {
          id: this.id,
          manage_name: this.manage_name,
          client_id: this.client_id,
          ticket_id: this.ticket_id,
          ticket_number: this.ticket_number,
          remain_number: this.remain_number,
          cancel_type_cd: this.cancel_type_cd,
          cancel_reason: this.cancel_reason,
          booking_date: this.booking_date
        };

        if(this.cancel_type_cd==2) {
          if (!req.ticket_id) return alert('이용권을 선택해주세요.');
          let err_msg = (this.ticket_type_cd==1) ? '이용권 잔여 금액은'+req.remain_number+'원 입니다.' : '이용권 잔여 횟수는'+req.remain_number+'회 입니다.';
          if (req.ticket_number > req.remain_number) return alert(err_msg);
        }

        $.ajax({
          url: '/schedule/action_cancel',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('취소가 완료되었습니다.');
              location.href = '/';
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_CANCEL_BOOK.mount('#popup_cancel_book');
</script>
