import mysql.connector, time, datetime
from pygame import mixer 

#Int sql connection
mydb = mysql.connector.connect(
  host="localhost",
  user="whizzplayeruser",
  password="Music2109!",
  database="whizzplayer"
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

def systemStartup():
    #Check if correct tables exist
    table1Sql = "SELECT count(*) FROM information_schema.tables WHERE table_name = 'Tracks' AND TABLE_SCHEMA = 'whizztest'"
    sqlfetch.execute(table1Sql)
    table1Result = sqlfetch.fetchall()[0][0]
    print(table1Result)
    if table1Result == 0:
        sqlfetch.execute("CREATE TABLE Tracks (TrackID int NOT NULL AUTO_INCREMENT, TrackDisplayName varchar(255) NOT NULL, SongFile varchar(255) NOT NULL, PRIMARY KEY (TrackID));")

    table2Sql = "SELECT count(*) FROM information_schema.tables WHERE table_name = 'Cues' AND TABLE_SCHEMA = 'whizztest'"
    sqlfetch.execute(table2Sql)
    table2Result = sqlfetch.fetchall()[0][0]
    if table2Result == 0:
        sqlfetch.execute("CREATE TABLE Cues (CueID int NOT NULL AUTO_INCREMENT, CueDisplayName varchar(255) NOT NULL, TrackID int NOT NULL, PlayTime time NOT NULL, PlayDate date NOT NULL, Repeats BOOLEAN NOT NULL, RepeatMon BOOLEAN, RepeatTue BOOLEAN, RepeatWed BOOLEAN, RepeatThu BOOLEAN, RepeatFri BOOLEAN, RepeatSat BOOLEAN, RepeatSun BOOLEAN, Enabled BOOLEAN NOT NULL, PRIMARY KEY (CueID), FOREIGN KEY (TrackID) REFERENCES Tracks(TrackID));")

    table3Sql = "SELECT count(*) FROM information_schema.tables WHERE table_name = 'Playing' AND TABLE_SCHEMA = 'whizztest'"
    sqlfetch.execute(table3Sql)
    table3Result = sqlfetch.fetchall()[0][0]
    if table3Result == 0:
        sqlfetch.execute("CREATE TABLE Playing (TrackID int NOT NULL, HaltTrack BOOLEAN NOT NULL);")

    table4Sql = "SELECT count(*) FROM information_schema.tables WHERE table_name = 'InstantPlay' AND TABLE_SCHEMA = 'whizztest'"
    sqlfetch.execute(table4Sql)
    table4Result = sqlfetch.fetchall()[0][0]
    if table4Result == 0:
        sqlfetch.execute("CREATE TABLE InstantPlay (TrackID int NOT NULL,Played BOOLEAN NOT NULL);")

    table5Sql = "SELECT count(*) FROM information_schema.tables WHERE table_name = 'Users' AND TABLE_SCHEMA = 'whizztest'"
    sqlfetch.execute(table5Sql)
    table5Result = sqlfetch.fetchall()[0][0]
    if table5Result == 0:
        sqlfetch.execute("CREATE TABLE Users (UserID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,Username VARCHAR(50) NOT NULL UNIQUE,Password VARCHAR(255) NOT NULL,CreatedDate DATETIME DEFAULT CURRENT_TIMESTAMP, LastLogin DATETIME, UserType int NOT NULL DEFAULT 0 );")

    #Check if correct default vaules exist
    table6Sql = "SELECT count(*) FROM Playing"
    sqlfetch.execute(table6Sql)
    table6Result = sqlfetch.fetchall()[0][0]
    if table6Result == 0:
        createTable6Sql = "INSERT INTO Playing (TrackID,HaltTrack) VALUES (0,False);"
        sqlfetch.execute(createTable6Sql)

    table7Sql = "SELECT count(*) FROM InstantPlay"
    sqlfetch.execute(table7Sql)
    table7Result = sqlfetch.fetchall()[0][0]
    if table7Result == 0:
        createTable7Sql = "INSERT INTO InstantPlay (TrackID,Played) VALUES (0,True);"
        sqlfetch.execute(createTable7Sql)

    sqlfetch.execute("UPDATE Playing SET HaltTrack = 0")
    sqlfetch.execute("UPDATE InstantPlay SET Played = 1")

    print("Startup Complete")

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
    systemStartup()
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