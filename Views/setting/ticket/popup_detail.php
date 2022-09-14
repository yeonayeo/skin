<div id="popup_detail" class="popup_wrap" style="width: 910px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_detail.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">이용권 상세</h1>
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
                <input type="text" placeholder="이용권명을 입력하세요" style="width: 250px;" v-model="name">
              </td>
            </tr>
            <tr>
              <td colspan="2"> <!-- 0719작업 -->
                <div class="row_box">
                  <div v-for="(count, index) in count_list" :class="count.class_type">
                    <div class="inner">
                      <div style="width: 35px; color: #b6b0ab;">ㄴ</div>
                      <div class="ticket">
                        <div class="row_tit required">사용가능</div> <!-- 0623 status 추가 -->
                        <div class="input">
                          <input type="number" placeholder="숫자만 입력하세요" v-model="count.number">
                          <span class="unit">회</span>
                        </div>
                      </div>
                      <div class="sel_price"> <!-- 0623 판매가 입력 추가 -->
                        <span class="row_tit required">판매가</span>
                        <div class="input">
                          <input type="number" placeholder="숫자만 입력하세요" v-model="count.sales_amount">
                          <span class="unit">원</span>
                        </div>
                      </div>
                      <div class="btns">
                        <button type="button" class="btn_delete" v-if="count_list.length > 1" @click="count_delete(count.id, index)"></button>
                        <button type="button" class="btn_add" v-if="count.add_btn " @click="count_list_add">추가</button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>비고</th>
              <td>
                <input type="text" style="width: 400px;" placeholder="내용을 입력하세요" v-model="note">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update">이용권 저장</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_detail.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_DETAIL = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},

        id: POPUP_RES.id,
        name: POPUP_RES.name,
        note: POPUP_RES.note,
        count_list: POPUP_RES.count_list,
        delete_kind_list: []
      }
    },
    mounted() {},
    methods: {
      count_list_add() {
        for (let count of this.count_list) {
          count.add_btn = false;
        }

        this.count_list.push({
          number: null,
          checkbox: false,
          class_type: 'add_row no_check',
          add_btn: true,
          is_no_check: true
        });
      },
      count_delete(id, index) {
        let add_btn = checkbox = false;
        if(this.count_list[index].add_btn) {
          add_btn = true;
        }
        if(this.count_list[index].checkbox) {
          checkbox = true;
        }

        this.count_list.splice(index, 1);
        if(add_btn) {
          this.count_list[this.count_list.length-1].add_btn = true;
        }
        if(checkbox) {
          this.count_list[0].checkbox = true;
          this.count_list[0].class_type = 'add_row';
        }
        this.delete_kind_list.push(id);
      },
      action_update() {
        let count_list = this.count_list;
        let req = {
          id: this.id,
          name: this.name,
          count_list: count_list,
          note: this.note,
          delete_kind_list: this.delete_kind_list
        };
        if (!req.name) return alert('이용권명을 입력하세요.');
        for (let count of req.count_list) {
          if (!count.number) return alert('사용가능 횟수를 전부 입력하세요.');
          if (!count.sales_amount) return alert('판매가를 전부 입력하세요.');
        }

        $.ajax({
          url: '/setting/ticket/action_update',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/ticket`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_DETAIL.mount('#popup_detail');
</script>
