<div id="popup_visit" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_visit.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head flex_area ai_c">
    <h1 class="popup_title">방문 정보</h1>
    <!-- 0621 방문정보 태그 추가 -->
    <span class="tag c1 ml10" v-if="!is_cancel">완료</span>
    <span class="tag e1 ml10" v-if="is_cancel">취소</span>
    <!-- end -->
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">고객 정보</div>
        <button type="button" v-if="client_id" class="btn_client_view" @click="popup_password()">상세 정보 보기</button>
      </div>
      <div class="content_body">
        <table>
          <tbody>
            <tr>
              <th>고객명</th>
              <td>{{ client_name }}</td>
            </tr>
            <tr>
              <th>연락처</th>
              <td>
                <div class="sms">
                  {{ client_hp }}
                  <a href="/setting/sms" class="btn_sms"></a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="area">
      <div class="content_head">
        <div class="content_title">관리 정보</div>
        <button type="button" class="btn_update_book" @click="popup_update_visit()">관리 정보 수정</button>
      </div>
      <div class="content_body">
        <table>
          <tbody>
            <tr>
              <th>예약일</th>
              <td>
                <div>{{ insert_date }} ({{ insert_week_txt }})<span class="desc">{{ booking_type }}</span></div>
              </td>
            </tr>
            <tr>
              <th>관리명</th>
              <td>{{ manage_name }}</td>
            </tr>
            <tr>
              <th>관리일</th>
              <td>{{ booking_date }} ({{ booking_week_txt }})</td>
            </tr>
            <tr> <!-- 0620 관리실 추가 -->
              <th>관리실</th>
              <td>{{ booking_room }}</td>
            </tr>
            <tr>
              <th>관리시간</th>
              <td>{{ booking_time }}</td>
            </tr>
            <tr>
              <th>이용권 정보</th>
              <td>
                <span class="tag c1 l mr10" v-if="is_direct">{{ direct_type }}</span>
                {{ ticket_name }}
                <span class="desc" v-if="is_visit">({{ visit_cnt }}회차 방문)</span> {{ subtraction_number }}
              </td>
            </tr>
            <tr v-if="is_add_sales"> <!-- 0719작업 -->
              <th style="vertical-align: top;">추가매출</th>
              <td>
                <span class="tag c1 l mr10">{{ add_sales_type }}</span>{{ add_sales_amount }}원
                <div class="pt5" v-if="add_admin_memo">{{ add_admin_memo }}</div>
              </td>
            </tr>
            <tr>
              <th>메모</th>
              <td>{{ memo }}</td>
            </tr>
            <tr>
              <th>특이사항</th>
              <td>{{ special_note }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn e1 l" style="width: 220px;" onclick="popup_visit.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_VISIT = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},
        id: POPUP_RES.id,
        client_id: POPUP_RES.client_id,
        client_name: POPUP_RES.client_name,
        client_hp: POPUP_RES.client_hp,
        insert_date: POPUP_RES.insert_date,
        insert_week_txt: POPUP_RES.insert_week_txt,
        booking_type: POPUP_RES.booking_type,
        manage_name: POPUP_RES.manage_name,
        booking_date: POPUP_RES.booking_date,
        booking_week_txt: POPUP_RES.booking_week_txt,
        booking_room: POPUP_RES.booking_room,
        booking_time: POPUP_RES.booking_time,
        ticket_name: POPUP_RES.ticket_name,
        visit_cnt: POPUP_RES.visit_cnt,
        subtraction_number: POPUP_RES.subtraction_number,
        memo: POPUP_RES.memo,
        special_note: POPUP_RES.special_note,
        cancel_type: POPUP_RES.cancel_type,
        cancel_reason: POPUP_RES.cancel_reason,
        is_cancel: POPUP_RES.is_cancel,
        add_sales_type: POPUP_RES.add_sales_type,
        add_sales_amount: POPUP_RES.add_sales_amount,
        add_admin_memo: POPUP_RES.add_admin_memo,
        is_add_sales: (POPUP_RES.add_sales_amount!=0) ? true : false,
        is_direct: (POPUP_RES.direct_type_cd) ? true : false,
        direct_type: POPUP_RES.direct_type,
      }
    },
    mounted() {},
    methods: {
      popup_password() {
        popup_password = sunrise({
          target: '/client/popup_password?client_id='+this.client_id
        })
      },
      popup_update_visit() {
        popup_update_visit = sunrise({
          target: '/schedule/popup_update_visit?id='+this.id+'&prev=schedule'
        })
      }
    }
  });

  POPUP_VISIT.mount('#popup_visit');
</script>
