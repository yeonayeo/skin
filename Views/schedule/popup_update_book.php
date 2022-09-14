<div id="popup_update_book" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_update_book.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">예약 정보 수정</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">고객 정보</div>
      </div>
      <div class="content_body readonly">
        <table>
          <tbody>
            <tr>
              <th>고객명</th>
              <td>{{ client_name }}</td>
            </tr>
            <tr>
              <th>연락처</th>
              <td>{{ client_hp }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="area">
      <div class="content_head">
        <div class="content_title">예약 정보</div>
      </div>
      <div class="content_body">
        <table>
          <tbody>
            <tr>
              <th>예약일</th>
              <td style="width: 140px;">{{ insert_date }} ({{ insert_week_txt }})</td>
              <th style="width: 70px; text-align: left;">예약 방식</th>
              <td>
                <select style="width: 180px;" v-model="booking_type_cd">
                  <option value="null">예약 방식을 선택하세요</option>
                  <option value="1">전화</option>
                  <option value="2">방문</option>
                  <option value="3">기타</option>
                </select>
              </td>
            </tr>
            <tr>
              <th class="required">관리명</th>
              <td colspan="3">
                <input type="text" placeholder="관리명을 입력하세요" v-model="manage_name">
              </td>
            </tr>
            <tr>
              <th class="required">관리일</th>
              <td colspan="3">
                <input type="text" id="kronos" style="width: 250px;" v-model="booking_date" ref="booking_date">
              </td>
            </tr>
            <tr>
              <th class="required">관리시간</th>
              <td colspan="3">
                <div class="flex_row">
                  <select style="width: 180px;" v-model="start_time_id">
                    <option value="null">시작시간 선택</option>
                    <option v-for="start in start_time_list" :value="start.id" :disabled="start.is_disabled">{{ start.name }}</option>
                  </select>
                  <span>~</span>
                  <select style="width: 180px;" v-model="end_time_id">
                    <option value="null">종료시간 선택</option>
                    <option v-for="end in end_time_list" :value="end.id" :disabled="end.is_disabled">{{ end.name }}</option>
                  </select>
                </div>
              </td>
            </tr>
            <tr>
              <th class="required" style="vertical-align: top; padding-top: 10px;">이용권</th>
              <td colspan="3">
                <div class="flex_row">
                  <select style="width: 250px;" v-model="client_ticket_id">
                    <option value=null>이용권 미선택</option>
                    <option v-for="(ticket, index) in ticket_list" :value="ticket.id">{{ ticket.name_txt }}</option>
                  </select>
                  <div class="flex_row" v-if="client_ticket_id"> <!-- 0719작업 -->
                    <input type="number" placeholder="-" style="width: 50px; text-align: right;" v-model="ticket_number">
                    <span class="span_txt ml5">차감</span>
                  </div>
                  <button type="button" class="btn_add" v-if="client_ticket_id" :disabled="is_add_sales" @click="add_sales">추가</button> <!-- 0719작업 -->
                </div>
                <div class="flex_row mt10" v-if="!client_ticket_id"> <!-- 0719작업 -->
                  <label class="radio">
                    <input type="radio" value="card" v-model="direct_type_cd">
                    <span>카드</span>
                  </label>
                  <label class="radio">
                    <input type="radio" value="money" v-model="direct_type_cd">
                    <span>현금</span>
                  </label>
                  <div class="flex_row">
                    <input type="number" placeholder="관리 금액을 입력하세요" style="width: 220px;" v-model="subtraction_number">
                    <span class="span_txt ml10">원</span>
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="client_ticket_id && is_add_sales"> <!-- 0719작업 -->
              <th style="vertical-align: top; padding-top: 15px;" >추가매출</th>
              <td colspan="3">
                <div class="flex_row">
                  <label class="radio">
                    <input type="radio" value="card" v-model="add_sales_type_cd">
                    <span>카드</span>
                  </label>
                  <label class="radio">
                    <input type="radio" value="money" v-model="add_sales_type_cd">
                    <span>현금</span>
                  </label>
                  <div class="flex_row">
                    <input type="number" placeholder="관리 금액을 입력하세요" style="width: 220px;" v-model="add_sales_amount">
                    <span class="span_txt ml10">원</span>
                  </div>
                </div>
                <div class="flex_row mt10">
                  <input type="text" placeholder="추가관리 내용을 입력하세요" v-model="add_admin_memo">
                </div>
              </td>
            </tr>
            <tr>
              <th>메모</th>
              <td colspan="3">
                <input type="text" placeholder="메모를 입력하세요" v-model="memo">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update">수정 완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" @click="popup_cancel_book()">예약 취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_UPDATE_BOOK = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},

        search_list: [],
        ticket_list: [],
        id: POPUP_RES.id,
        client_type_cd: 1,
        client_id: POPUP_RES.client_id,
        client_name: POPUP_RES.client_name,
        client_hp: POPUP_RES.client_hp,
        insert_date: POPUP_RES.insert_date,
        insert_week_txt: POPUP_RES.insert_week_txt,
        booking_date: POPUP_RES.booking_date,
        booking_room_cd: POPUP_RES.booking_room_cd,
        booking_type_cd: POPUP_RES.booking_type_cd,
        manage_name: POPUP_RES.manage_name,
        client_ticket_id: POPUP_RES.client_ticket_id,
        ticket_number: POPUP_RES.subtraction_number,
        subtraction_number: (POPUP_RES.client_ticket_id) ? null : POPUP_RES.subtraction_number,
        memo: POPUP_RES.memo,
        start_time_list: POPUP_RES.start_time_list,
        end_time_list: POPUP_RES.end_time_list,
        ticket_list: POPUP_RES.ticket_list,
        start_time_id: POPUP_RES.start_time_id,
        end_time_id: POPUP_RES.end_time_id,
        is_add_sales: (POPUP_RES.add_sales_amount>0) ? true : false,
        direct_type_cd: POPUP_RES.direct_type_cd,
        add_sales_type_cd: POPUP_RES.add_sales_type_cd,
        add_sales_amount: POPUP_RES.add_sales_amount,
        add_admin_memo: POPUP_RES.add_admin_memo,
      }
    },
    watch: {
      booking_date(n) {
        if(n) {
          if(this.booking_room_cd) {
            this.search_schedule();
          }
        }
      },
      booking_room_cd(n) {
        if(n) {
          this.search_schedule();
        }
      },
      client_ticket_id(n) {
        if(n) {
          this.ticket_number = null;
          if(n == "null") {
            this.client_ticket_id = null;
          }
        }
      },
    },
    mounted() {
        $('#kronos').kronos({
          onChange: date => {
            this.booking_date = this.$refs.booking_date.value;
          }
        });
      },
    methods: {
      add_sales() {
        this.is_add_sales = true;
      },
      popup_cancel_book() {
        popup_cancel_book = sunrise({
          target: '/schedule/popup_cancel_book?id='+this.id
        })
      },
      search_schedule() {
        $.ajax({
          url: '/search/schedule_time',
          data: {schedule_id: this.id, booking_date: this.booking_date, booking_room_cd: this.booking_room_cd},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.start_time_id = null;
              this.start_time_list = res.data.start_time_list;
              this.end_time_id = null;
              this.end_time_list = res.data.end_time_list;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      popup_book(id) {
        popup_book = sunrise({
          data: {},
          target: '/schedule/popup_book?id='+id
        })
      },
      action_update() {
        let req = {
          id: this.id,
          booking_type_cd: this.booking_type_cd,
          manage_name: this.manage_name,
          booking_date: this.booking_date,
          booking_room_cd: this.booking_room_cd,
          start_time_id: parseInt(this.start_time_id),
          end_time_id: parseInt(this.end_time_id),
          ticket_id: this.client_ticket_id,
          ticket_number: this.ticket_number,
          subtraction_number: this.subtraction_number,
          memo: this.memo,
          direct_type_cd: this.direct_type_cd,
          add_sales_type_cd: this.add_sales_type_cd,
          add_sales_amount: this.add_sales_amount,
          add_admin_memo: this.add_admin_memo
        };

        if (!req.manage_name) return alert('관리명을 입력해주세요');
        if (!req.booking_date) return alert('관리일을 선택해주세요');
        if (!req.booking_room_cd) return alert('관리실을 선택해주세요');
        if (!req.start_time_id) return alert('관리 시작시간을 선택해주세요');
        if (!req.end_time_id) return alert('관리 종료시간을 선택해주세요');
        if (req.ticket_id && !req.ticket_number) return alert('이용권 차감금액 또는 횟수를 입력해주세요');
        if (!req.ticket_id && !req.subtraction_number) return alert('관리금액을 입력해주세요');
        if(req.start_time_id >= req.end_time_id || (req.end_time_id-req.start_time_id) <= 1) return alert('관리시간은 1시간 이상 올바르게 선택해 주세요');

        $.ajax({
          url: '/schedule/action_update',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('수정이 완료되었습니다.');
              popup_update_book.sunrise('closePopup');
              popup_book.sunrise('closePopup');
              this.popup_book(req.id);
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_UPDATE_BOOK.mount('#popup_update_book');
</script>
