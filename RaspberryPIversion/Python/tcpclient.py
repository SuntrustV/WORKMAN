import socket
import sys
import random
import time
from crypt_t import AESCipher
from datetime import datetime

import sqlite3
import json

class SCTCPClient:

    conn = object()
    sqldb = object()
    key_auth = '0123456789abcdefghijklmn'
    key_msg_out = '01ghijklmnob123456123456'
    key_msg_in = '01ghijklmnob123456123456'
    device_key = '3'
    pincode = '1234'
    message_id = ''
    paramstr = ''

    def __init__(self):
        self.conn = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.conn.setsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF, 8192)
        # Connect the socket to the port where the server is listening
        server_address = ('37.143.10.57', 2347)
        print('connecting to %s port %s' % server_address)
        self.conn.connect(server_address)
        print('successfully connected to %s port %s' % server_address)
        self.sqldb = sqlite3.connect('smarttsn.db')
        self.sqldb.row_factory = sqlite3.Row



    def auth(self):
        # Send data
        print('sending auth...')
        self.send_message(1, self.pincode+'/'+self.device_key+'/'+'3.7'+'/'+datetime.strftime(datetime.now(), "%Y.%m.%d %H:%M:%S"))
        res = self.receive(1)
        ar = res.split('/')
        self.message_id = ar[1]

    def send_message(self, method_id, message, error_id=0):
        # Send data
        pwd = self.key_auth if (method_id == 1) else self.key_msg_out
        data = str(method_id)+'/'+str(self.message_id)+'/'+str(error_id)+'/'+str(message)
        crypted_msg ='{ "data": "'+ AESCipher(pwd).encrypt(data).decode('utf-8')+'","ver":"1.A"}'
        self.conn.send(bytes(crypted_msg.encode('utf-8')))

    def receive(self, key=2):
        pwd = self.key_auth if (key == 1) else self.key_msg_in
        # Look for the response
        data = self.conn.recv(1024)
        decrypted_msg = AESCipher(pwd).decrypt(data).decode('utf-8')
        return decrypted_msg 

    def close(self):
        # Close socket
        self.conn.close()

    def send_table(self, tableName, rowCount):
        with self.sqldb:
             cur = self.sqldb.cursor()
             if (self.paramstr=='nocheck'):
                 cur.execute('SELECT *  FROM  '+tableName+'  limit '+str(rowCount))
             else:
                 cur.execute('SELECT *  FROM '+tableName+' where synchro=""  limit '+str(rowCount))

             rows = cur.fetchall()
             if (len(rows)>0):
                st = json.dumps([dict(ix) for ix in rows])
                st = '{ "tableName" : "'+tableName+'", "data": '+ st +"}"
                time.sleep(1);
                self.send_message(3, st)
                res = self.receive(3)
                ar = res.split('/')
                self.message_id = ar[1]
                for val in rows:
                    id = val['id']
                    st = "update  "+tableName+" set synchro='"+self.message_id+" "+datetime.strftime(datetime.now(), "%Y.%m.%d %H:%M:%S")+"' where id="+str(id)
                    cur.execute(st)
                          
#######################################################################
###
### Main part of program
###
#######################################################################
sock = SCTCPClient()

for param in sys.argv:
    if (param=='--nocheck'):
       sock.paramstr='nocheck'

sock.auth()
print('update tables...')
sock.send_table('log_device_data',5)
sock.send_table('log_events',5)
sock.send_table('log_speed_data',5)
sock.send_table('log_video',5)
sock.close()
