<div id="popup_cosmetic_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_cosmetic_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">구매 내역 추가</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">제품 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th class="required">제품명</th>
              <td>
                <div class="input_search" style="width: 250px; margin-left: 0;">
                  <input type="search" placeholder="제품명을 검색하세요" style="width: 250px;" v-model="name" @keypress.enter="search_cosmetic">
                  <button type="button" class="btn_input_search" @click="search_cosmetic"></button>
                  <!-- 검색결과 -->
                  <div class="search_result">
                    <ul class="search_list">
                      <li v-for="(cosmetic, index) in search_list" @click="search_cosmetic_info(cosmetic.id)">
                        <div class="name" v-html="cosmetic.name_txt"></div>
                      </li>
                    </ul>
                  </div>
                  <!-- END -->
                </div>
              </td>
            </tr>
            <tr>
              <th class="required">수량</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" placeholder="" style="width: 110px;" v-model="quantity">
                  <div class="span_txt ml10">개</div>
                </div>
              </td>
            </tr>
            <tr>
              <th class="required">구매일</th>
              <td>
                <input type="text" id="kronos" style="width: 250px;" v-model="purchase_date" ref="purchase_date">
              </td>
            </tr>
            <tr>
              <th class="required">판매자</th>
              <td>
                <select style="width: 250px;" v-model="manager_id">
                  <option :value="null">판매자를 선택하세요.</option>
                  <option v-for="manager in manager_list" :value="manager.id">{{ manager.name }}</option>
                </select>
              </td>
            </tr>
            <tr> <!-- 0719작업 -->
              <th class="required">결제수단</th>
              <td>
                <div class="flex_area ai_c" style="min-height: 30px;">
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
            </tr>
            <tr> <!-- 0719작업 -->
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
              <td>
                <input type="text" style="width: 100%;" v-model="memo" ref="memo">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_regist">등록하기</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_cosmetic_regist.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_COSMETIC_REGIST = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},
        client_id: POPUP_RES.client_id,
        purchase_date: POPUP_RES.purchase_date,
        manager_list: POPUP_RES.manager_list,
        manager_id: null,
        name: null,
        search_list: [],
        cosmetic_id: null,
        quantity: 1,
        memo: null,
        remain_quantity: null,
        discount_rate: null,
        payment_method_cd: 'card',
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
      search_cosmetic() {
        if (!this.name) return alert('제품명을 입력해주세요.');
        $.ajax({
          url: '/search/cosmetic',
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
      search_cosmetic_info(cosmetic_id) {
        $.ajax({
          url: '/search/cosmetic_info',
          data: {cosmetic_id: cosmetic_id},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.name = res.data.name;
              this.cosmetic_id = cosmetic_id;
              this.remain_quantity = res.data.remain_quantity;
              this.search_list = [];
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      action_regist() {
        let req = {
          client_id: this.client_id,
          cosmetic_id: this.cosmetic_id,
          cosmetic_name: this.name,
          quantity: this.quantity,
          purchase_date: this.purchase_date,
          manager_id: this.manager_id,
          manager_name: this.manager_name,
          remain_quantity: this.remain_quantity,
          memo: this.memo,
          payment_method_cd: this.payment_method_cd,
          discount_rate: this.discount_rate
        };
        if (!req.client_id) return alert('고객을 선택하세요.');
        if (!req.cosmetic_id) return alert('제품을 선택하세요.');
        if (!req.quantity) return alert('수량을 입력하세요.');
        if (!req.purchase_date) return alert('구매일 선택하세요.');
        if (!req.manager_id) return alert('판매자를 선택하세요.');
        if(req.quantity > req.remain_quantity) return alert('재고보다 수량이 많습니다.');

        $.ajax({
          url: '/client/action_regist_cosmetic',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/client/detail?id=`+this.client_id;
            } else {
              alert(res.err_msg);
              console.log(res);
            }
          }
        });
      }
    }
  });

  POPUP_COSMETIC_REGIST.mount('#popup_cosmetic_regist');
</script>
