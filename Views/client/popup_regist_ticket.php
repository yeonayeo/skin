<div id="popup_regist_ticket" class="popup_wrap" style="width: 910px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist_ticket.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">이용권 추가</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">이용권 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th class="required">이용권명</th>
              <td>
                <div class="input_search2">
                  <input type="search" placeholder="이용권명을 검색하세요" style="width: 250px;" v-model="name" @keypress.enter="search_ticket">
                  <button type="button" class="btn_input_search" @click="search_ticket"></button>
                  <!-- 검색결과 -->
                  <div class="search_result">
                    <ul class="search_list">
                      <li v-for="(ticket, index) in search_list" v-html="ticket.name_txt" @click="search_ticket_kind(ticket.id, ticket.name)"></li>
                      <!-- <li class="disabled"><span class="keyword">바디</span>케어권</li> --> <!--disabled 어떤상황?-->
                    </ul>
                  </div>
                  <!-- END -->
                </div>
              </td>
              <th class="required">결제일</th>
              <td>
                <input type="text" id="kronos" style="width: 250px;" v-model="purchase_date" ref="purchase_date">
              </td>
            </tr>
            <tr> <!-- 0719작업 -->
              <th class="required">종류</th> <!-- v-if="ticket_id" -->
              <td>
                <select style="width: 250px;" :disabled="count_list.length<=0" v-model="kind_id">
                  <option v-for="(count, index) in count_list" :key="index" :value="count.id">{{count.number}}회</option>
                </select>
              </td>
              <th class="required">수량</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" style="width: 250px; text-align: right;" placeholder="-" v-model="quantity">
                  <div class="span_txt ml10">개</div>
                </div>
              </td>
            </tr>
            <tr> <!-- 0719작업 -->
              <th class="required">결제수단</th>
              <td>
                <div class="flex_area ai_c">
                  <label class="radio">
                    <input type="radio" value="card" v-model="payment_method_cd">
                    <span>카드</span>
                  </label>
                  <label class="radio ml30">
                    <input type="radio" value="money" v-model="payment_method_cd">
                    <span>현금</span>
                  </label>
                </div>
              </td>
              <th>할인 여부</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" style="width: 250px;" placeholder="할인율을 입력하세요" v-model="discount_rate">
                  <div class="span_txt ml10">%</div>
                </div>
              </td>
            </tr>
            <tr>
              <th>메모</th>
              <td colspan="3">
                <input type="text" placeholder="메모를 입력하세요" style="width: 400px;" v-model="memo">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_regist">등록하기</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_regist_ticket.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_REGIST_TICKET = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},

        client_id: POPUP_GET.id,
        ticket_id: null,
        name: null,
        search_list: [],
        purchase_date: null,
        kind_id: null,
        memo: null,
        count_list: [],
        count_kind_id: null,
        quantity: null,
        discount_rate: null,
        payment_method_cd: 'card'
      }
    },
    mounted() {
      $('#kronos').kronos({
        onChange: date => {
          this.purchase_date = this.$refs.purchase_date.value;
        }
      });
    },
    methods: {
      search_ticket() {
        if (!this.name) return alert('이용권명을 입력해주세요.');
        $.ajax({
          url: '/search/ticket',
          data: {name: this.name},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.search_list = res.data;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      search_ticket_kind(ticket_id, ticket_name) {
        $.ajax({
          url: '/search/ticket_kind',
          data: {ticket_id: ticket_id},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.count_list = res.data.count_list;

              this.name = ticket_name,
              this.ticket_id = ticket_id;
              this.search_list = [];

              this.kind_id = res.data.count_list[0].id;
              this.count_kind_id = res.data.count_list[0].id;

            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      action_regist() {
        let req = {
          client_id: this.client_id,
          ticket_id: this.ticket_id,
          ticket_name: this.name,
          kind_id: this.kind_id,
          purchase_date: this.purchase_date,
          memo: this.memo,
          payment_method_cd: this.payment_method_cd,
          quantity: this.quantity,
          discount_rate: this.discount_rate
        };
        if (!req.client_id) return alert('고객을 선택하세요.');
        if (!req.ticket_id) return alert('이용권을 선택하세요.');
        if (!req.kind_id) return alert('종류를 선택하세요.');
        if (!req.purchase_date) return alert('결제일을 선택하세요.');
        if (!req.quantity) return alert('수량을 입력하세요.');
        $.ajax({
          url: '/client/action_regist_ticket',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/client/detail?id=`+this.client_id;
            } else {
              alert(res.err_msg);
            }
          }
        });

      }
    }
  });

  POPUP_REGIST_TICKET.mount('#popup_regist_ticket');
</script>
