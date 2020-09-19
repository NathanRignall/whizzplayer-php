import mysql.connector, time, datetime
from pygame import mixer 

#Int sql connection
mydb = mysql.connector.connect(
  host="localhost",
  user="musicplayer",
  password="Music1234",
  database="MusicPlayerCore"
)

mydb.autocommit = True
sqlfetch = mydb.cursor()

#Int aduio player
mixer.init()
mixer.music.set_volume(0.5) 
musicFolder = "/var/www/whizzplayer/tracks/"

#global vars
timeLastPlay = 0
loopItter = 0

def playSong(fileNameEnding,trackID):
    global timeLastPlay
    loadSongFileName = musicFolder + fileNameEnding
    if mixer.music.get_busy() == 0:
        mixer.music.load(loadSongFileName)
        mixer.music.play()
        timeLastPlay = 60
        nowPlayingSql = "UPDATE Playing SET TrackID =" + str(trackID)
        sqlfetch.execute(nowPlayingSql)
        print("Playing song: ", fileNameEnding)
    else:
        print("Error Channel Busy")

def setInstantFetch():
    #Check if audio playback needs to be halted
    fetchPlayingSql = ("SELECT HaltTrack FROM Playing")
    sqlfetch.execute(fetchPlayingSql)
    playingResult = sqlfetch.fetchall()
    HaltTrack = playingResult[0][0]
    #If halt is false
    if HaltTrack == 0:
        #Get Instant Play Details
        fetchInsatntPlaySql = ("SELECT * FROM InstantPlay")
        sqlfetch.execute(fetchInsatntPlaySql)
        insatntPlayResult = sqlfetch.fetchall()
        TrackID = insatntPlayResult[0][0]
        Played = insatntPlayResult[0][1]
        #If instant play song hasn't played
        if Played == 0:
            #If fetch instant songfile name
            fetchTrackSql = ("SELECT TrackID,SongFile FROM Tracks WHERE TrackID=" + str(TrackID))
            sqlfetch.execute(fetchTrackSql)
            trackResult = sqlfetch.fetchall()
            try:
                #If song is found play song and set played to True
                SongFile = trackResult[0][1]
                instantPlaySql = "UPDATE InstantPlay SET Played = 1"
                sqlfetch.execute(instantPlaySql)
                playSong(SongFile,TrackID)
            except IndexError as e:
                print("Instant track not found")
        else:
            ##print("No instant track")
            t = 1
    else:
        #Stop music on halt and set halted to False
        mixer.music.stop()
        playingSql = "UPDATE Playing SET HaltTrack = 0"
        sqlfetch.execute(playingSql)
        print("Playback Halted")

def cueInstantFetch():
    #Get current time and date
    datetoday = datetime.date.today()
    timenowpre = time.localtime()
    timenow = time.strftime("%H:%M:00", timenowpre)
    #Fetch sql data for cues
    fetchCueSql = ("SELECT CueID,TrackID,CueDisplayName FROM Cues WHERE Enabled=True AND Repeats=False AND PlayTime='" + str(timenow) + "' AND PlayDate='" + str(datetoday) + "'")
    sqlfetch.execute(fetchCueSql)
    cueResult = sqlfetch.fetchall()
    #If cue is found with correct date and time set vars
    foundCue = False
    try:
        CueID = cueResult[0][0]
        TrackID = cueResult[0][1]
        DisplayName = cueResult[0][2]
        foundCue = True
    except IndexError as e:
        print("No cue found", timenow)
    #Fetch sql for track song filename if cue is found
    if foundCue == True :
        fetchTrackSql = ("SELECT TrackID,SongFile FROM Tracks WHERE TrackID=" + str(TrackID))
        sqlfetch.execute(fetchTrackSql)
        trackResult = sqlfetch.fetchall()
        SongFile = trackResult[0][1]
        playSong(SongFile,TrackID)

def cueRepeatFetch():
    #Get current time and date
    datetoday = datetime.date.today()
    weekday = datetoday.weekday()
    if weekday == 0:
        daysql = "RepeatMon=1"
    elif weekday == 1:
        daysql = "RepeatTue=1"
    elif weekday == 2:
        daysql = "RepeatWed=1"
    elif weekday == 3:
        daysql = "RepeatThu=1"
    elif weekday == 4:
        daysql = "RepeatFri=1"
    elif weekday == 5:
        daysql = "RepeatSat=1"
    elif weekday == 6:
        daysql = "RepeatSun=1"
    timenowpre = time.localtime()
    timenow = time.strftime("%H:%M:00", timenowpre)
    #Fetch sql data for cues
    fetchCueSql = ("SELECT CueID,TrackID,CueDisplayName FROM Cues WHERE Enabled=True AND Repeats=True AND PlayTime='" + str(timenow) + "' AND PlayDate<='" + str(datetoday) + "' AND " + str(daysql))
    sqlfetch.execute(fetchCueSql)
    cueResult = sqlfetch.fetchall()
    #If cue is found with correct date and time set vars
    foundCue = False
    try:
        CueID = cueResult[0][0]
        TrackID = cueResult[0][1]
        DisplayName = cueResult[0][2]
        foundCue = True
    except IndexError as e:
        print("No repeat cue found", timenow)
    #Fetch sql for track song filename if cue is found
    if foundCue == True :
        fetchTrackSql = ("SELECT TrackID,SongFile FROM Tracks WHERE TrackID=" + str(TrackID))
        sqlfetch.execute(fetchTrackSql)
        trackResult = sqlfetch.fetchall()
        SongFile = trackResult[0][1]
        playSong(SongFile,TrackID)

def slowLoop():
    global timeLastPlay
    if timeLastPlay > 0:
        timeLastPlay = timeLastPlay - 10
        print("waiting timeout...")
    elif timeLastPlay == 0:
        cueInstantFetch()
        cueRepeatFetch()

def fastLoop():
    setInstantFetch()
    if mixer.music.get_busy() == 0:
        nowPlayingSql = "UPDATE Playing SET TrackID = 0"
        sqlfetch.execute(nowPlayingSql)

try:
    print("STARTUP!!")
    while True:
        fastLoop()
        if loopItter == 0:
            slowLoop()
            loopItter = 10
        elif loopItter > 0:
            loopItter = loopItter - 1
        time.sleep(1)
except KeyboardInterrupt:
    print("Exit") 