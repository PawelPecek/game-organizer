import { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableWithoutFeedback } from 'react-native';
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useIsFocused } from '@react-navigation/native';
import SearchIco from '../svg/SearchIco';
import AddIco from '../svg/AddIco';
import ItemWiadomosci from '../components/ItemWiadomosci';
import BottomBar from '../components/BottomBar';

const Wiadomosci = ({navigation}) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const [list, setList] = useState({
        label: "użytkownicy",
        data: []
    });
    const isFocused = useIsFocused();
    const setUser = ()=>{
        if(list.label == 'grupy') {
            db.find({}, (err, docs)=>{
                fetch("http://10.0.2.2:8000/api/message/user/list", {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify(docs[0])
                })
                .then(response => response.json())
                .then(data =>{
                    if(data.status="ok") {
                        console.log(data)
                        setList({
                            label: "użytkownicy",
                            data: data.data
                        });
                    }else{
                        console.log(data);
                    }
                })
                .catch(err=>console.log);
            })
        }
    };
    const setGroup = ()=>{
        if(list.label == 'użytkownicy') {
            db.find({}, (err, docs)=>{
                fetch("http://10.0.2.2:8000/api/message/group/list", {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify(docs[0])
                })
                .then(response => response.json())
                .then(data =>{
                    if(data.status="ok") {
                        console.log(data)
                        setList({
                            label: "grupy",
                            data: data.data
                        });
                    }else{
                        console.log(data);
                    }
                })
                .catch(err=>console.log);
            })
        }
    };
    const searchAction = ()=>{

    };
    const addAction = ()=>{

    };
    useEffect(()=>{
        db.find({}, (err, docs)=>{
            if(isFocused) {
                fetch("http://10.0.2.2:8000/api/message/user/list", {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify(docs[0])
                })
                .then(response => response.json())
                .then(data =>{
                    if(data.status="ok") {
                        console.log(data)
                        setList({
                            label: "użytkownicy",
                            data: data.data
                        });
                    }else{
                        console.log(data);
                    }
                })
                .catch(err=>console.log);
            }
        });
    },  [isFocused]);
    return (
        <View style={style.main}>
            <View style={style.topBar}>
                <View>
                    <TouchableWithoutFeedback onPress={searchAction}>
                        <View>
                            <SearchIco />
                        </View>
                    </TouchableWithoutFeedback>
                </View>
                <View>
                    <TouchableWithoutFeedback onPress={addAction}>
                        <View style={(list.label == "użytkownicy") && style.invisible}>
                            <AddIco />
                        </View>
                    </TouchableWithoutFeedback>
                </View>
            </View>
            <View style={style.tabRow}>
                <TouchableWithoutFeedback onPress={setUser}>
                    <View style={(list.label == "użytkownicy") ? style.tabActive : style.tabInactive}>
                        <Text style={style.text}>Użytkownicy</Text>
                    </View>
                </TouchableWithoutFeedback>
                <TouchableWithoutFeedback onPress={setGroup}>
                    <View style={(list.label == "grupy") ? style.tabActive : style.tabInactive}>
                        <Text style={style.text}>Grupy</Text>
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <ScrollView style={style.list}>
                {
                    (list.label == "użytkownicy") &&
                        list.data.map(el=><ItemWiadomosci key={el.id} name={el.sender} avatar="" />)
                }
                {
                    (list.label == "grupy") &&
                        list.data.map(el=><ItemWiadomosci key={el} name={el} avatar="" />)
                }
            </ScrollView>
            <BottomBar navigation={navigation} activeOption="Wiadomosci"/>
        </View>
    )
}

const style = StyleSheet.create({
    main:{
        width: "100%",
        height: "100%",
        flexDirection: "column",
        alignItems: "center",
        backgroundColor: "#1c1c1c"
    },
    topBar: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "space-between",
        paddingTop: 10,
        paddingLeft: 10,
        paddingRight: 10,
        paddingBottom: 10
    },
    tabRow: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "space-around",
        alignItems: "center",
        marginTop: 15
    },
    tabActive: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        opacity: 1
    },
    tabInactive: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        opacity: 0.4
    },
    text: {
        color: "white",
        fontSize: 20
    },
    list: {
        width: "100%",
        marginTop: 15
    },
    invisible: {
        opacity: 0
    }
});

export default Wiadomosci;