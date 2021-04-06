Ветка ПО для работы под Raspberry PI OS

Параметр запуска --nocheck Python-скрипта tcpclient.py  отключает проверку поля synchro в синхронизируемых таблицах.
Это упрощает тестирование системы.

# WORKMAN
Во всех таблицах:<br>
  LOG_DEVICE_DATA<br>
  LOG_EVENTS<br>
  LOG_SPEED_DATA<br>
  LOG_VIDEO<br>
баз данных, как SQLite так и PostgreSQL должны быть поля:<br>
id - Integer<br>
и synchro - varchar (75)<br>
<br>
!!! У всех синхронизируемых таблиц должны быть одинаковые поля !!!<br>
