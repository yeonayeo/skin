<div id="popup_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">일정 등록</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">고객 정보</div>
      </div>
      <div class="content_body regist mb40">
        <div class="row">
          <label class="radio">
            <input type="radio" value="1" v-model="client_type_cd">
            <span>고객 정보 검색</span>
          </label>
          <div class="input_search" v-if="client_type_cd==1"> <!-- 고객정보검색 선택시에만 노출 -->
            <input type="search" placeholder="고객명으로 검색하세요" style="width: 310px;" v-model="client_name" @keypress.enter="search_client" :disabled="client_type_cd==2">
            <button type="button" class="btn_input_search" @click="search_client"></button>
            <!-- 검색결과 -->
            <div class="search_result">
              <ul class="search_list">
                <li v-for="(client, index) in search_list" @click="selected_client(client.id, client.name)">
                  <div class="name" v-html="client.name_txt"></div>
                  <div class="phone">{{ client.hp }}</div>
                </li>
              </ul>
            </div>
            <!-- END -->
          </div>
        </div>
        <div class="row mt10" style="position: relative;">
          <label class="radio" style="position: absolute; top: 10px;">
            <input type="radio" value="2" v-model="client_type_cd">
            <span>직접 입력</span>
          </label>
          <div class="search_box" v-if="client_type_cd==2"> <!-- 직접입력 선택시에만 노출 -->
            <div class="input_search">
              <input type="search" placeholder="고객명을 입력하세요" style="width: 310px; padding: 0 10px;" :disabled="client_type_cd==1" v-model="input_client_name">
            </div>
            <div class="input_search mt10">
              <input type="text" placeholder="연락처를 입력하세요" style="width: 310px; padding: 0 10px;" :disabled="client_type_cd==1" v-model="client_hp">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="area">
      <div class="content_head">
        <div class="content_title">예약 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th>예약 방식</th>
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
              <td>
                <input type="text" placeholder="관리명을 입력하세요" v-model="manage_name">
              </td>
            </tr>
            <tr>
              <th class="required">관리일</th>
              <td>
                <input type="text" id="kronos" style="width: 250px;" v-model="booking_date" ref="booking_date">
              </td>
            </tr>
            <tr> <!-- 0620 관리실 선택창 추가 -->
              <th class="required">관리실</th>
              <td>
                <select style="width: 250px;" v-model="booking_room_cd">
                  <option value="null">관리실을 선택하세요</option>
                  <option value="1">Room 1</option>
                  <option value="2">Room 2</option>
                  <option value="3">Room 3</option>
                  <option value="4">Room 4</option>
                  <option value="5">Room 5</option>
                  <option value="6">Room 6</option>
                  <option value="7">Room 7</option>
                  <option value="8">Room 8</option>
                  <option value="9">VIP</option>
                </select>
              </td>
            </tr>
            <tr>
              <th class="required">관리시간</th>
              <td>
                <div class="flex_row">
                  <select style="width: 180px;" v-model="start_time_id">
                    <option value="null">시작시간 선택</option>
                    <option v-for="start in start_time_list" :value="start.id" :disabled="start.is_disabled">{{ start.name }}</option>
                  </select>
                  <span>~</span>
                  <select style="width: 180px;" v-model="end_time_id">
                    <option value="null">종료시간 선택</option>
                    <option v-for="end in end_time_list" :value="end.id" :disabled="end.is_disabled">{{ end.name }}</option>
                    <!-- <option disabled>오후 3:00</option> -->
                  </select>
                </div>
              </td>
            </tr>
            <tr>
              <th class="required" style="vertical-align: top; padding-top: 15px;">이용권</th>
              <td>
                <div class="flex_row">
                  <select style="width: 240px;" v-model="ticket_id">
                    <option value=null>이용권 미선택</option>
                    <option v-for="(ticket, index) in ticket_list" :value="ticket.id">{{ ticket.name_txt }}</option>
                  </select>
                  <div class="flex_row" v-if="ticket_id"> <!-- 0719작업 -->
                    <input type="number" placeholder="-" style="width: 50px; text-align: right;" v-model="ticket_number">
                    <span class="span_txt ml5">차감</span>
                  </div>
                  <button type="button" class="btn_add" v-if="ticket_id" :disabled="is_add_sales" @click="add_sales">추가</button> <!-- 0719작업 -->
                </div>
                <div class="flex_row mt10" v-if="!ticket_id"> <!-- 0719작업 -->
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
            <tr v-if="ticket_id && is_add_sales"> <!-- 0719작업 -->
              <th style="vertical-align: top; padding-top: 15px;" >추가매출</th>
              <td>
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
              <td>
                <input type="text" placeholder="메모를 입력하세요" v-model="memo">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">일정 등록</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_regist.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_REGIST = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},

        search_list: [],
        ticket_list: [],
        client_type_cd: 1,
        client_id: null,
        client_name: null,
        input_client_name: null, // 직접입력
        client_hp: null,
        booking_date: POPUP_RES.booking_date,
        booking_room_cd: null,
        booking_type_cd: null,
        manage_name: null,
        ticket_id: null,
        ticket_number: null,
        subtraction_number: null,
        memo: null,
        start_time_list: POPUP_RES.start_time_list,
        end_time_list: POPUP_RES.end_time_list,
        start_time_id: null,
        end_time_id: null,
        is_add_sales: false,
        add_sales_type_cd: 'card',
        add_sales_amount: null,
        add_admin_memo: null,
        direct_type_cd: 'card',
      }
    },
    watch: {
      client_type_cd(n) {
        if (n && n==2) {
          // this.client_id = null;
        }
      },
      client_hp(n) {
        if (n) {
          this.client_hp = this.client_hp.replaceAll(/[^0-9]/g, '');
        }
      },
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
      ticket_id(n) {
        if(n && n == "null") {
          this.ticket_id = null;
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
      search_client() {
        if (!this.client_name) return alert('고객명을 입력해주세요.');
        $.ajax({
          url: '/search/client',
          data: {name: this.client_name},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.search_list = res.data;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      selected_client(id, name) {
        this.client_id = id;
        this.client_name = name;
        this.search_list = [];

        $.ajax({
          url: '/search/client_ticket',
          data: {client_id: id},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.ticket_list = res.data;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      search_schedule() {
        $.ajax({
          url: '/search/schedule_time',
          data: {booking_date: this.booking_date, booking_room_cd: this.booking_room_cd},
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
      action_regist() {
        let req = {
          client_type_cd: this.client_type_cd,
          client_id: (this.client_type_cd==1) ? this.client_id : null,
          client_name: (this.client_type_cd==1) ? this.client_name : this.input_client_name,
          client_hp: (this.client_type_cd==2) ? this.client_hp : null,
          booking_type_cd: this.booking_type_cd,
          manage_name: this.manage_name,
          booking_date: this.booking_date,
          booking_room_cd: this.booking_room_cd,
          start_time_id: parseInt(this.start_time_id),
          end_time_id: parseInt(this.end_time_id),
          ticket_id: this.ticket_id,
          ticket_number: this.ticket_number,
          direct_type_cd: this.direct_type_cd,
          subtraction_number: this.subtraction_number,
          memo: this.memo,
          add_sales_type_cd: this.add_sales_type_cd,
          add_sales_amount: this.add_sales_amount,
          add_admin_memo: this.add_admin_memo
        };
        req.client_id = (req.client_type_cd==1) ? req.client_id : null;

        if (req.client_type_cd==1 && !req.client_id) return alert('고객정보를 입력하세요');
        if (req.client_type_cd==2 && !req.client_name) return alert('고객명을 입력하세요');
        if (req.client_type_cd==2 && !req.client_hp) return alert('연락처를 입력해주세요');
        if (!req.manage_name) return alert('관리명을 입력해주세요');
        if (!req.booking_date) return alert('관리일을 선택해주세요');
        if (!req.booking_room_cd) return alert('관리실을 선택해주세요');
        if (!req.start_time_id) return alert('관리 시작시간을 선택해주세요');
        if (!req.end_time_id) return alert('관리 종료시간을 선택해주세요');
        if (req.ticket_id && !req.ticket_number) return alert('이용권 차감금액 또는 횟수를 입력해주세요');
        if (!req.ticket_id && !req.subtraction_number) return alert('관리금액을 입력해주세요');
        if(req.start_time_id >= req.end_time_id || (req.end_time_id-req.start_time_id) <= 1) return alert('관리시간은 1시간 이상 올바르게 선택해 주세요');

        $.ajax({
          url: '/schedule/action_regist',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_REGIST.mount('#popup_regist');
</script>
