<div id="popup_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">제품 등록</h1>
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
                <input type="text" placeholder="제품명을 입력하세요" v-model="name">
              </td>
            </tr>
            <tr>
              <th class="required">입고가</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" placeholder="숫자만 입력하세요" style="width: 220px;" v-model="purchase_price">
                  <div class="span_txt ml10">원</div>
                </div>
              </td>
            </tr>
            <tr>
              <th class="required">판매가</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" placeholder="숫자만 입력하세요" style="width: 220px;" v-model="sales_price">
                  <div class="span_txt ml10">원</div>
                </div>
              </td>
            </tr>
            <!-- <tr>
              <th class="required">수수료</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" placeholder="숫자만 입력하세요" style="width: 220px;" v-model="fee">
                  <div class="span_txt ml10">원</div>
                </div>
              </td>
            </tr> -->
            <tr>
              <th>메모</th>
              <td>
                <input type="text" placeholder="내용을 입력하세요" v-model="memo">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">제품 등록</button>
      <button type="button" class="btn e1 l" style="width: 110px;" onclick="popup_regist.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_REGIST = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        name: null,
        purchase_price: null,
        sales_price: null,
        fee: null,
        memo: null,
      }
    },
    mounted() {},
    methods: {
      action_regist() {
        let req = {
          name: this.name,
          purchase_price: this.purchase_price,
          sales_price: this.sales_price,
          fee: this.fee,
          memo: this.memo
        };
        if (!req.name) return alert('제품명을 입력하세요.');
        if (!req.purchase_price) return alert('입고가를 입력하세요.');
        if (!req.sales_price) return alert('판매가를 입력하세요.');
        if(req.fee!=0) {
          if (!req.fee || req.fee=='' || req.fee==undefined) return alert('수수료 입력하세요.');
        }

        $.ajax({
          url: '/setting/cosmetic/action_regist',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/cosmetic`;
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
