package sdk

import (
    "net"
    "bytes"
    "encoding/binary"
    "io"
    "fmt"
    "strconv"
)

type DClient struct {
    Host       string
    Port       int
    Api        string
    Sync       bool
    CtrlName   string
    MethodName string
    HeaderLen  int
    Conn       net.Conn
    Chan       chan []string
    Debug      bool
}

func GetClient(host string, port int) *DClient {
    c := make(chan []string)
    cli := &DClient{
        host,
        port,
        "proxy/proxy",
        true,
        "a",
        "m",
        4,
        nil,
        c,
        false,
    }
    cli.Connect()
    return cli
}

func (cli *DClient) SetApi(api string) {
    cli.Api = api
}

func (cli *DClient) NoSync() {
    cli.Sync = false
}

func (cli *DClient) SetDebug() {
    cli.Debug = true
}

func (cli *DClient) Pack(_data string) []byte {
    buf := new(bytes.Buffer)
    var data = []interface{}{
        uint32(len(_data)),
        []byte(_data),
    }
    for _, v := range data {
        err := binary.Write(buf, binary.BigEndian, v)
        if nil != err {
            str := "ERROR: pack " + _data + "error :" + err.Error()
            panic(str)
        }
    }
    return buf.Bytes()
}

func (cli *DClient) Unpack(b []byte) ([]string, []byte, bool) {
    dlen := len(b);
    data := make([]string, 0)
    ok := false
    start := 0
    var b_buf *bytes.Buffer
    var x int32
    for {
        b_buf = bytes.NewBuffer(b[start:cli.HeaderLen])
        binary.Read(b_buf, binary.BigEndian, &x)
        //x = int(x)
        x += int32(cli.HeaderLen)
        start += int(x)
        if int(x) == dlen {
            //正好一个完整包
            data = append(data, string(b[start - int(x) + cli.HeaderLen:int(x)]))
            ok = true;
            break
        }

        if int(x) > dlen {
            //不足一个包
            start -= int(x)
            break;
        }
        ok = true
        dlen -= int(x)
        data = append(data, string(b[start - int(x) + cli.HeaderLen:int(x)]))
    }
    return data, b[start:], ok
}

func (cli *DClient) Connect() {
    conn, err := net.Dial("tcp", cli.Host+":"+strconv.Itoa(cli.Port))
    if nil != err {
        panic("connect server error")
    }
    cli.Conn = conn
}

func (cli *DClient) Send(data string) (int) {
    wrote := cli.Pack(data);
    _slen, err := cli.Conn.Write(wrote)
    if nil != err {
        println(err.Error())
    }
    return _slen
}

func (cli *DClient) Recv() {
    var b [4096]byte
    var ret bytes.Buffer
    for {
        rlen, err := cli.Conn.Read(b[0:])
        if nil == err {
            if rlen > 0 {
                ret.Write(b[:rlen])
                result, more, ok := cli.Unpack(ret.Bytes())
                if (ok) {
                    cli.Chan <- result
                }
                if len(more) > 0 {
                    ret.Reset()
                    ret.Write(more)
                }

            }
        } else {
            if (err == io.EOF) {

            } else {
                break;
            }
        }
    }
}

func (cli *DClient) Call(method string, data map[string]string) []string {
    var sendData string;
    _recv := "0"
    if (cli.Sync) {
        _recv = "1"
    }
    sendData = "{\"_recv\":" + _recv + ","
    for k, v := range data {
        sendData += "\"" + k + "\":\"" + v + "\","
    }
    sendData += " \"" + cli.CtrlName + "\":\"" + cli.Api + "\", \"" + cli.MethodName + "\":\"" + method + "\"}"
    cli.Log("send:%s", sendData)
    cli.Send(sendData)
    go cli.Recv()
    result := <-cli.Chan
    return result
}

func (cli *DClient) Log(format string, a ...interface{}) {
    if cli.Debug {
        fmt.Printf(format, a ...)
    }
}