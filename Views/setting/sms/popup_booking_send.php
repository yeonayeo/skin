<div id="popup_booking_send" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_booking_send.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">예약 발송</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_body mb40" style="padding: 0; border: none;">
        <table>
          <tbody>
            <tr>
              <th>예약 설정</th>
              <td>
                <label class="switch">
                  <input type="checkbox" v-model="is_reserved">
                  <span></span>
                </label>
              </td>
            </tr>
            <tr>
              <th>발송 대상</th>
              <td>
                <input type="text" style="width: 250px;" value="해당일 예약 고객 전체" v-model="target" disabled>
              </td>
            </tr>
            <tr>
              <th class="required" style="vertical-align: top; padding-top: 10px;">발송 시간</th>
              <td>
                <div class="flex_row">
                  <select style="width: 250px;" v-model="send_type_cd">
                    <option value="1">방문 전일</option>
                    <option value="2">방문 당일</option>
                    <option value="3">방문 후일</option>
                    <option value="4">방문 일주일 후</option>
                    <option value="5">방문 한달 후</option>
                  </select>
                </div>
                <div class="flex_row mt10">
                  <select style="width: 250px;" v-model="send_time">
                    <option value="09:00:00">오전 9:00</option>
                    <option value="10:00:00">오전 10:00</option>
                    <option value="11:00:00">오전 11:00</option>
                    <option value="12:00:00">오후 12:00</option>
                    <option value="13:00:00">오후 1:00</option>
                    <option value="14:00:00">오후 2:00</option>
                    <option value="15:00:00">오후 3:00</option>
                    <option value="16:00:00">오후 4:00</option>
                    <option value="17:00:00">오후 5:00</option>
                    <option value="18:00:00">오후 6:00</option>
                    <option value="19:00:00">오후 7:00</option>
                  </select>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_booking_send">설정 저장</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_booking_send.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_BOOKING_SEND = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        id: POPUP_RES.id,
        is_reserved: POPUP_RES.is_reserved,
        target: POPUP_RES.target,
        send_type_cd: POPUP_RES.send_type_cd,
        send_time: (POPUP_RES.send_time) ? POPUP_RES.send_time : "09:00:00"
      }
    },
    mounted() {},
    methods: {
      action_booking_send() {
        let req = {
          id: this.id,
          is_reserved: this.is_reserved,
          send_type_cd: this.send_type_cd,
          send_time: this.send_time
        };

        if(!req.send_type_cd || !req.send_time) return alert('발송시간을 선택해주세요.');
        $.ajax({
          url: '/setting/sms/action_booking_send',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('저장되었습니다.');
              location.href = `/setting/sms`;
            } else {
              console.log(res);
            }
          }
        });
      }
    }
  });

  POPUP_BOOKING_SEND.mount('#popup_booking_send');
</script>
