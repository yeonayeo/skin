<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="main_container" v-cloak>
    <div class="page_head">
      <h2 class="page_title">예약 관리</h2>
      <div class="input_search">
        <input type="search" placeholder="고객명을 검색하세요" style="width: 360px;" v-model="search_name" @keypress.enter="action_search">
        <button type="button" class="btn_input_search" @click="action_search"></button>
      </div>
    </div>
    <div class="schedule_wrap">
      <section class="schedule_area">
        <div class="schedule_navi">
          <button type="button" class="navi" @click="selected_date(prev_ymd)"><i class="material-icons">keyboard_arrow_left</i></button>
          <div class="date_text">{{month}}월 {{day}}일 {{week_txt}}</div>
          <button type="button" class="navi" @click="selected_date(next_ymd)"><i class="material-icons">keyboard_arrow_right</i></button>
          <button type="button" class="btn_today" @click="action_today">오늘</button>
        </div>
        <div class="schedule_body">
          <div class="time_box">
            <div class="time am">9:00</div>
            <div class="time">10:00</div>
            <div class="time">11:00</div>
            <div class="time pm">12:00</div>
            <div class="time">1:00</div>
            <div class="time">2:00</div>
            <div class="time">3:00</div>
            <div class="time">4:00</div>
            <div class="time">5:00</div>
            <div class="time">6:00</div>
            <div class="time">7:00</div>
            <div class="time">8:00</div>
            <div class="time">9:00</div>
          </div>
          <div class="schedule_box">
            <ul class="room_list">
              <li class="room_name">Room 1</li>
              <li class="room_name">Room 2</li>
              <li class="room_name">Room 3</li>
              <li class="room_name">Room 4</li>
              <li class="room_name">Room 5</li>
              <li class="room_name">Room 6</li>
              <li class="room_name">Room 7</li>
              <li class="room_name">Room 8</li>
              <li class="room_name">VIP</li>
            </ul>
            <div class="room_schedule_board">
              <div class="col" v-for="(room, room_idx) in list"> <!-- room-->
                <!-- class추가 : 소요시간별 크기 지정 & 완료된 예약 표시
                  소요시간 : hr1 / hr2 / hr3 / hr4
                  완료된예약 : visited
                 -->
                <!-- style : 위치 지정
                  top: calc(60px * n); // n(0~12행까지), 30분 단위(n.5)
                 -->
                <div v-for="(time, time_idx) in room" :class="time.class" :style="time.style" @click="popup_info(time.id, time.status_cd)">
                  <div class="desc">
                    <div class="name">{{ time.client_name }}<span>고객님</span></div>
                    <div class="type">{{ time.manage_name }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="calendar_area">
        <div class="inner">
          <div class="calendar_box">
            <div class="calendar_navi">
              <button type="button" class="navi" @click="get_calendar(calendar_info.prev_y, calendar_info.prev_m)"><i class="material-icons">keyboard_arrow_left</i></button>
              <div class="month_text">{{calendar_info.year}}년 {{calendar_info.month_txt}}월</div>
              <button type="button" class="navi" @click="get_calendar(calendar_info.next_y, calendar_info.next_m)"><i class="material-icons">keyboard_arrow_right</i></button>
            </div>
            <div class="calendar_body">
              <table>
                <thead>
                  <tr>
                    <th>S</th>
                    <th>M</th>
                    <th>T</th>
                    <th>W</th>
                    <th>T</th>
                    <th>F</th>
                    <th>S</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(week, index) in calendar_info.calendar" :key="index">
                    <td v-for="(date, idx) in week.date_list" :key="idx" :class="date.class" @click="selected_date(date.ymd)">{{date.date}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <button type="button" class="btn c1 l btn_regist mt20" @click="popup_regist()">일정 등록</button>
        <div class="notice_box">
          <div class="title">스킨스타 공지사항</div>
          <div class="content">
            {{ notice.contents }}
          </div>
        </div>
      </section>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          get: GET,
          req: {},
          err: {},
          calendar_info: RES.calendar_info,
          ymd: RES.ymd,
          prev_ymd: RES.prev_ymd,
          next_ymd: RES.next_ymd,
          month: RES.month,
          day: RES.day,
          week_txt: RES.week_txt,
          today: RES.ymd,
          notice: RES.notice,
          list: RES.list,
          search_name: null
        }
      },
      mounted() {},
      methods: {
        popup_info(id, status_cd) {
          if(status_cd==1) {
            this.popup_book(id);
          } else if(status_cd==3) {
            this.popup_visit(id);
          }
        },
        popup_book(id) {
          popup_book = sunrise({
            data: {},
            target: '/schedule/popup_book?id='+id
          })
        },
        popup_visit(id) {
          popup_visit = sunrise({
            data: {},
            target: '/schedule/popup_visit?id='+id
          })
        },
        popup_regist() {
          popup_regist = sunrise({
            data: {},
            target: '/schedule/popup_regist?booking_date='+this.ymd
          })
        },
        action_today() {
          location.href = `/`;
        },
        action_search() {
          if(!this.search_name) return alert('고객명을 입력해주세요.');
          location.href = `/schedule/search?name=`+this.search_name;
        },
        get_calendar(year, month) {
          $.ajax({
            url: '/search/calendar',
            data: {
              year: year,
              month: month
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.calendar_info = res.data;
              } else {
                console.log(res);
              }
            }
          });
        },
        selected_date(ymd) {
          // 공지사항 데이터 가져오기
          $.ajax({
            url: '/setting/admin/get_notice_info',
            data: {
              notice_date: ymd
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.notice = res.data;
              } else {
                console.log(res);
              }
            }
          });

          // 스케줄 가져오기
          $.ajax({
            url: '/schedule/get_schedule_info',
            data: {
              ymd: ymd
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.month = res.data.month;
                this.day = res.data.day;
                this.week_txt = res.data.week_txt;
                this.prev_ymd = res.data.prev_ymd;
                this.next_ymd = res.data.next_ymd;
                this.list = res.data.list;

                for (let week of res.data.calendar_info.calendar) {
                  for (let dt of week.date_list) {
                    if(dt.is_today) {
                      dt.class = '';
                      dt.is_today = false;
                    }
                    if(dt.ymd == ymd) {
                      dt.class = 'today';
                      dt.is_today = true;
                    }
                  }
                }
                this.ymd = ymd;
                this.calendar_info = res.data.calendar_info;
              } else {
                console.log(res);
              }
            }
          });


        },
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
