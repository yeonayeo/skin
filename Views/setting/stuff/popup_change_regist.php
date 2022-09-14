<div id="popup_change_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_change_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">변경내역 추가</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_body mb40">
        <table>
          <colgroup>
            <col style="width: 90px;">
            <col style="width: 180px;">
            <col style="width: 75px;">
            <col style="width: auto;">
          </colgroup>
          <tbody>
            <tr>
              <th class="required">날짜</th>
              <td colspan="3">
                <input type="text" id="kronos" style="width: 250px;" v-model="goods_date" ref="goods_date">
              </td>
            </tr>
            <tr>
              <th class="required">구분</th>
              <td>
                <select style="width: 120px;" v-model="type_cd">
                  <option :value="1">입고</option>
                  <option :value="2" v-if="remain_quantity>0">소진</option>
                </select>
              </td>
              <th class="required">수량</th>
              <td>
                <div class="flex_area ai_c">
                  <input type="number" style="width: 110px;" v-model="quantity">
                  <div class="span_txt ml10">개</div>
                </div>
              </td>
            </tr>
            <tr>
              <th>담당자</th>
              <td colspan="3">
                <select style="width: 250px;" v-model="manager_id">
                  <option :value="null">없음</option>
                  <option v-for="manager in manager_list" :value="manager.id">{{ manager.name }}</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>비고</th>
              <td colspan="3">
                <input type="text" placeholder="내용을 입력하세요" v-model="note">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">재고 저장</button>
      <button type="button" class="btn e1 l" style="width: 110px;" onclick="popup_change_regist.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_CHANGE_REGIST = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        stuff_id: POPUP_RES.stuff_id,
        goods_date: POPUP_RES.goods_date,
        manager_list: POPUP_RES.manager_list,
        remain_quantity: POPUP_RES.remain_quantity,
        type_cd: 1,
        quantity: null,
        note: null,
        manager_id: null,
      }
    },
    mounted() {
      $('#kronos').kronos({
        onChange: date => {
          this.goods_date = this.$refs.goods_date.value;
        }
      });
    },
    methods: {
      action_regist(type) {
        let req = {
          stuff_id: this.stuff_id,
          goods_date: this.goods_date,
          type_cd: this.type_cd,
          quantity: this.quantity,
          manager_id: this.manager_id,
          note: this.note
        };
        if (!req.goods_date) return alert('날짜를 선택하세요.');
        if (!req.type_cd) return alert('구분을 선택하세요.');
        if (!req.quantity) return alert('수량을 선택하세요.');
        if (req.type_cd==2 && (req.quantity > this.remain_quantity)) {
          return alert('재고보다 수량이 많습니다.');
        }

        $.ajax({
          url: '/setting/stuff/action_regist_stock',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/stuff/detail?id=`+this.stuff_id;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_CHANGE_REGIST.mount('#popup_change_regist');
</script>
