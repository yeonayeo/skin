<div id="popup_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">비품 등록</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">비품 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th class="required">비품명</th>
              <td>
                <input type="text" placeholder="비품명 입력하세요" v-model="name">
              </td>
            </tr>
            <tr>
              <th class="required">구분</th>
              <td>
                <input type="text" placeholder="구분을 입력하세요" v-model="type">
              </td>
            </tr>
            <tr> <!-- 0719작업 -->
              <th class="required">입고가</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" placeholder="숫자만 입력하세요" style="width: 220px;" v-model="purchase_price">
                  <div class="span_txt ml10">원</div>
                </div>
              </td>
            </tr>
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
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">비품 등록</button>
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
        type: null,
        memo: null,
        purchase_price: null,
      }
    },
    mounted() {},
    methods: {
      action_regist() {
        let req = {
          name: this.name,
          type: this.type,
          memo: this.memo,
          purchase_price: this.purchase_price
        };
        if (!req.name) return alert('비품명 입력하세요.');
        if (!req.type) return alert('구분을 입력하세요.');
        if (!req.purchase_price) return alert('입고가를 입력하세요.');

        $.ajax({
          url: '/setting/stuff/action_regist',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/stuff`;
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
