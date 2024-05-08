Set Arg = WScript.Arguments
set WshShell = createObject("Wscript.Shell")
Set Inp = WScript.Stdin
Set Outp = Wscript.Stdout

if LCase(Arg(0)) = "web" or LCase(Arg(0)) = "http" then
    HttpGet
Elseif LCase(Arg(0)) = "remhtml" or LCase(Arg(0)) = "tags" then
    RemoveHTMLTags
End If


Sub HttpGet
On Error Resume Next
    Set File = WScript.CreateObject("Microsoft.XMLHTTP")
    File.Open "GET", Arg(1), False
    File.setRequestHeader "User-Agent", "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 1.1.4322; .NET CLR 3.5.30729; .NET CLR 3.0.30618; .NET4.0C; .NET4.0E; BCD2000; BCD2000)"
    File.Send
    txt=File.ResponseText
    'Putting in line endings
    Outp.write txt
    If err.number <> 0 then 
        Outp.writeline "" 
        Outp.writeline "Error getting file" 
        Outp.writeline "==================" 
        Outp.writeline "" 
        Outp.writeline "Error " & err.number & "(0x" & hex(err.number) & ") " & err.description 
        Outp.writeline "Source " & err.source 
        Outp.writeline "" 
        Outp.writeline "HTTP Error " & File.Status & " " & File.StatusText
        Outp.writeline  File.getAllResponseHeaders
        Outp.writeline LCase(Arg(1))
    End If
End Sub

Sub RemoveHTMLTags
    Set ie = CreateObject("InternetExplorer.Application") 
    ie.Visible = 0
    ie.Silent = 1 
    ie.Navigate2 "file://" & FilterPath & "Filter.html"
    Do 
        wscript.sleep 50            
    Loop Until ie.document.readystate = "complete"
    ie.document.body.innerhtml = Inp.readall
    Outp.write ie.document.body.innertext
'   ie.quit
End Sub