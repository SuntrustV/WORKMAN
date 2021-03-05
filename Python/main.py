import sqlite3
import json

conn = sqlite3.connect('smarttsn.db')
conn.row_factory = sqlite3.Row
with conn:
    cur = conn.cursor()
    cur.execute('SELECT *  FROM  log_device_data')
    rows = cur.fetchall()

    print(json.dumps([dict(ix) for ix in rows] ))

    conn.commit()

    #for row in rows:
    #    print(row)


