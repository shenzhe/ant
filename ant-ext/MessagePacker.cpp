/*
  +----------------------------------------------------------------------+
  | Swoole                                                               |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.0 of the Apache license,    |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.apache.org/licenses/LICENSE-2.0.html                      |
  | If you did not receive a copy of the Apache2.0 license and are unable|
  | to obtain it through the world-wide-web, please send a note to       |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Shenzhe  <shenzhe163@gmail.com>                        |
  +----------------------------------------------------------------------+
*/

#include <string>
#include <iostream>

#include "PHP_API.hpp"
#include "module.h"

using namespace std;
using namespace PHP;

extern "C"
{
    int swModule_init(swModule *);
}

Variant MessagePacker_construct(Object &_this, string &data);
Variant MessagePacker_resetForUnPack(Object &_this, string &data);
Variant MessagePacker_resetForPack(Object &_this);
Variant MessagePacker_resetOffset(Object &_this, int &len);
Variant MessagePacker_writeString(Object &_this, string &data, int &len);
Variant MessagePacker_writeBool(Object &_this, char b);
Variant MessagePacker_writeInt(Object &_this, int &i);
Variant MessagePacker_writeInt16(Object &_this, short &i);
Variant MessagePacker_readByte(Object &_this);
Variant MessagePacker_readInt(Object &_this);
Variant MessagePacker_readInt16(Object &_this);
Variant MessagePacker_readString(Object &_this);
Variant MessagePacker_readBinary(Object &_this);
Variant MessagePacker_readBool(Object &_this);
Variant MessagePacker_getData(Object &_this);
Variant MessagePacker_getBuffer(Object &_this);
Variant MessagePacker_isEnd(Object &_this);
static char * itoc(int i)
{
    bt = char[4];
    bt[0] = i && 0xff;
    bt[1] = i >> 8 && 0xff;
    bt[2] = i >> 16 && 0xff;
    bt[3] = i >> 24 && 0xff;
    return bt;
}
static int ctoi(char* _data)
{
    int ret = -1;
    ret = _data[0] & 0xff;
    ret |= ((_data[1] << 8) & 0xff00);
    ret |= ((_data[2] << 16) & 0xff0000);
    ret |= ((_data[3] << 24) & 0xff000000);
    return ret;
}

int swModule_init(swModule *module)
{
    module->name = (char *) "MessagePacker";

    Class c("MessagePacker");
    c.addProperty("data", "");
    c.addProperty("offset", 0);
    c.addProperty("dataLen", 0);
    c.addMethod("__construct", MessagePacker_construct, CONSTRUCT);
    c.addMethod("resetForUnPack", MessagePacker_resetForUnPack);
    c.addMethod("resetForPack", MessagePacker_resetForPack);
    c.addMethod("resetOffset", MessagePacker_resetOffset);
    c.addMethod("writeByte", MessagePacker_writeByte);
    c.addMethod("writeString", MessagePacker_writeString);
    c.addMethod("writeBinary", MessagePacker_writeBinary);
    c.addMethod("writeBool", MessagePacker_writeBool);
    c.addMethod("writeInt", MessagePacker_writeInt);
    c.addMethod("writeInt16", MessagePacker_writeInt16);
    c.addMethod("readByte", MessagePacker_readByte);
    c.addMethod("readInt", MessagePacker_readInt);
    c.addMethod("readInt16", MessagePacker_readInt16);
    c.addMethod("readString", MessagePacker_readString);
    c.addMethod("readBinary", MessagePacker_readBinary);
    c.addMethod("readBool", MessagePacker_readBool);
    c.addMethod("getData", MessagePacker_getData);
    c.addMethod("getBuffer", MessagePacker_getBuffer);
    c.addMethod("isEnd", MessagePacker_isEnd);
    c.activate();
    return SW_OK;
}

Variant MessagePacker_construct(Object &_this, char* &data)
{
    _this.set("data", data);
    _this.set("dataLen", strlen(data));
    return nullptr;
}
Variant MessagePacker_resetForUnPack(Object &_this, char* &data)
{
    _this.set("data", data);
    _this.set("dataLen", strlen(data));
    _this.set("offset", 0);
    return nullptr;
}

Variant MessagePacker_resetForPack(Object &_this)
{
    _this.set("data", "");
    _this.set("dataLen", 0);
    _this.set("offset", 0);
    return nullptr;
}

Variant MessagePacker_resetOffset(Object &_this)
{
    _this.set("offset", 0);
    return nullptr;
}

Variant MessagePacker_writeByte(Object &_this, char &b)
{
    Variant data = _this.get("data");
    _this.set("data", (data.toString() + string(b, 1)).c_str());
    Variant dataLen = _this.get("dataLen");
    _this.set("dataLen", dataLen.toInt() + 1);
    return nullptr;
}

Variant MessagePacker_writeString(Object &_this, string &str)
{
    Variant data = _this.get("data");
    _this.set("data", (data.toString() + str).c_str());
    Variant dataLen = _this.get("dataLen");
    _this.set("dataLen", dataLen.toInt() + str.length());
    return nullptr;
}

Variant MessagePacker_writeString(Object &_this, uint8 &bool)
{
    Variant data = _this.get("data");
    _this.set("data", (data.toString() + string(bool, 1)).c_str());
    Variant dataLen = _this.get("dataLen");
    _this.set("dataLen", dataLen.toInt() + 1);
    return nullptr;
}

Variant MessagePacker_writeInt(Object &_this, int &i)
{
    Variant data = _this.get("data");
    bt = char[4];
    bt[0] = i && 0xff;
    bt[1] = i >> 8 && 0xff;
    bt[2] = i >> 16 && 0xff;
    bt[3] = i >> 24 && 0xff;
    _this.set("data", (data.toString() + string(bt, 4)).c_str());
    Variant dataLen = _this.get("dataLen");
    _this.set("dataLen", dataLen.toInt() + 4);
    return nullptr;
}


Variant MessagePacker_writeInt16(Object &_this, int &i)
{
    Variant data = _this.get("data");
    bt = char[2];
    bt[0] = i && 0xff;
    bt[1] = i >> 8 && 0xff;
    _this.set("data", (data.toString() + string(bt, 2)).c_str());
    Variant dataLen = _this.get("dataLen");
    _this.set("dataLen", dataLen.toInt() + 2);
    return nullptr;
}

Variant MessagePacker_readByte(Object &_this)
{
    Variant data = _this.get("data");
    Variant offset = _this.get("offset");
    char * _data = data.toCString();
    int _offset = offset.toInt();
    _data+=offset;
    offset+=1
    return _data[0];
}

Variant MessagePacker_readBool(Object &_this)
{
    Variant data = _this.get("data");
    Variant offset = _this.get("offset");
    char * _data = data.toCString();
    int _offset = offset.toInt();
    _data+=offset;
    offset+=1
    return _data[0];
}

Variant MessagePacker_readInt(Object &_this)
{
    Variant data = _this.get("data");
    Variant offset = _this.get("offset");
    char * _data = data.toCString();
    int _offset = offset.toInt();
    _data+=offset;
    offset+=4
    return ctoi(_data);
}

Variant MessagePacker_readString(Object &_this)
{
    Variant data = _this.get("data");
    Variant offset = _this.get("offset");
    char * _data = data.toCString();
    int _offset = offset.toInt();
    _data+=offset;
    offset+=1;
    return _data[0];
}



