#!/usr/bin/env python3

from hashlib import md5
from base64 import b64decode
from base64 import b64encode

from Crypto.Cipher import AES
from Crypto.Random import get_random_bytes
from Crypto.Util.Padding import pad, unpad

class AESCipher:
    def __init__(self, key):
        self.key = key.encode('utf8')
        #print(key.encode('utf8'))
        #self.key = md5(key.encode('utf8')).digest()


    def encrypt(self, data):
        iv = get_random_bytes(AES.block_size)
        # print(AES.block_size)
        self.cipher = AES.new(self.key, AES.MODE_CBC, iv)
        # print('iv:', b64encode(iv))
        # print(pad(data.encode('utf-8'), AES.block_size))
        return b64encode(iv + self.cipher.encrypt(pad(data.encode('utf-8'),
            AES.block_size)))



    def decrypt(self, data):
        raw = b64decode(data)
        # print('iv:', b64encode(raw[:AES.block_size]))
        self.cipher = AES.new(self.key, AES.MODE_CBC, raw[:AES.block_size])
        # print("start_decryption")
        #return self.cipher.decrypt(raw[AES.block_size:])
        return unpad(self.cipher.decrypt(raw[AES.block_size:]), AES.block_size)




if __name__ == '__main__':

    print('TESTING ENCRYPTION')
    msg = input('Message...: ')
    pwd = input('Password..: ')
    # msg = "1/0003/00/8738/000000001213696"
    # pwd = "0123456789abcdefghijklmn"
    # print(len(pwd.encode('utf-8')))
    print('Ciphertext:', AESCipher(pwd).encrypt(msg).decode('utf-8'))

    print('\nTESTING DECRYPTION')
    cte = input('Ciphertext: ')
    pwd = input('Password..: ')
    # pwd = "0123456789abcdefghijklmn"
    print('Message...:', AESCipher(pwd).decrypt(cte).decode('utf-8'))








