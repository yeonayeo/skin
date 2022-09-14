<div id="popup_update_visit" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_update_visit.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">관리 정보 수정</h1>
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
        <div class="content_title">관리 정보</div>
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
              <th>관리일</th>
              <td>{{ booking_date }} ({{ booking_week_txt }})</td>
            </tr>
            <tr> <!-- 0620 관리실 추가 -->
              <th>관리실</th>
              <td colspan="3">{{ booking_room }}</td>
            </tr>
            <tr>
              <th>관리시간</th>
              <td colspan="3">{{ booking_time }}</td>
            </tr>
            <tr>
              <th>이용권</th>
              <td colspan="3">
                <span class="tag c1 l mr10" v-if="is_direct">{{ direct_type }}</span>
                {{ ticket_name }}
                <span class="desc" v-if="is_visit">({{ visit_cnt }}회차 방문)</span> {{ subtraction_number }}
              </td>
            </tr>
            <tr v-if="is_add_sales"> <!-- 0722추가 -->
              <th style="vertical-align: top; padding-top: 8px;">추가매출</th>
              <td colspan="3">
                <div class="flex_row jc_fs ai_c">
                  <span class="tag c1 l mr10">{{ add_sales_type }}</span>{{ add_sales_amount }}원
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
            <tr>
              <th>특이사항</th>
              <td colspan="3">
                <input type="text" placeholder="특이사항 입력하세요" v-model="special_note">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update_visit">수정 완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_update_visit.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_UPDATE_VISIT = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},

        search_list: [],
        ticket_list: [],
        id: POPUP_RES.id,
        visit_id: POPUP_RES.visit_id,
        client_type_cd: 1,
        client_id: POPUP_RES.client_id,
        client_name: POPUP_RES.client_name,
        client_hp: POPUP_RES.client_hp,
        insert_date: POPUP_RES.insert_date,
        insert_week_txt: POPUP_RES.insert_week_txt,
        booking_type_cd: POPUP_RES.booking_type_cd,
        booking_date: POPUP_RES.booking_date,
        booking_week_txt: POPUP_RES.booking_week_txt,
        booking_room: POPUP_RES.booking_room,
        booking_time: POPUP_RES.booking_time,
        ticket_name: POPUP_RES.ticket_name,
        manage_name: POPUP_RES.manage_name,
        memo: POPUP_RES.memo,
        special_note: POPUP_RES.special_note,
        prev: POPUP_RES.prev,
        is_direct: (POPUP_RES.direct_type_cd) ? true : false,
        direct_type: POPUP_RES.direct_type,
        add_sales_type: POPUP_RES.add_sales_type,
        add_sales_amount: POPUP_RES.add_sales_amount,
        add_admin_memo: POPUP_RES.add_admin_memo,
        is_add_sales: (POPUP_RES.add_sales_amount!=0) ? true : false,
      }
    },
    mounted() {},
    methods: {
      popup_visit(id) {
        popup_visit = sunrise({
          data: {},
          target: '/schedule/popup_visit?id='+id
        })
      },
      popup_detail_visit(id) {
        popup_detail_visit = sunrise({
          data: {},
          target: '/client/popup_detail_visit?id='+id
        })
      },
      action_update_visit() {
        let req = {
          id: this.id,
          visit_id: this.visit_id,
          booking_type_cd: this.booking_type_cd,
          manage_name: this.manage_name,
          memo: this.memo,
          special_note: this.special_note,
          add_admin_memo: this.add_admin_memo
        };

        if (!req.manage_name) return alert('관리명을 입력해주세요');

        $.ajax({
          url: '/schedule/action_update_visit',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('수정이 완료되었습니다.');
              if(this.prev=='client') {
                popup_update_visit.sunrise('closePopup');
                popup_detail_visit.sunrise('closePopup');
                this.popup_detail_visit(req.id);
              } else {
                popup_update_visit.sunrise('closePopup');
                popup_visit.sunrise('closePopup');
                this.popup_visit(req.id);
              }
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_UPDATE_VISIT.mount('#popup_update_visit');
</script>
