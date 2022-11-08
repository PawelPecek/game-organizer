import { useState, useEffect } from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableWithoutFeedback } from 'react-native';
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DocumentPicker from 'react-native-document-picker';
import SearchIco from '../svg/SearchIco';
import AddIco from '../svg/AddIco';
import BottomBar from '../components/BottomBar';

const Konto = ({navigation}) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const [list, setList] = useState({
        label: "Dołączone",
        data: []
    });
    const searchAction = ()=>{}
    const addAction = ()=>{}
    const changeAvatar = ()=>{
        DocumentPicker.pickSingle({
            type: [DocumentPicker.types.images]
        })
        .then(res=>{
            db.find({}, (err, docs)=>{
                const formData = new FormData();
           //     formData.append('login', docs[0].login);
                formData.append('password', docs[0].password);
                formData.append('avatar', res);
                fetch('http://10.0.2.2:8000/api/user/change/avatar', {
                    method: 'post',
                    body: formData,
                    headers: {
                       'Content-Type': 'multipart/form-data; ',
                        },
                    })
                    .then(res=>res.json())
                    .then(res=>console.log)
                    .catch(err=>console.log);
            });
        });
    }
    const changeLogin = ()=>{
        navigation.navigate("FormChangeLogin");
    }
    const changePassword = ()=>{
        navigation.navigate("FormChangePassword");
    }
    const logout = ()=>{
        navigation.navigate("FormLogout");
    }
    useEffect(()=>{
        
    });
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
                        <View style={(list.label == "Dołączone") && style.invisible}>
                            <AddIco />
                        </View>
                    </TouchableWithoutFeedback>
                </View>
            </View>
            <View>
                <Text>XD</Text>
            </View>
            <View style={style.userConfig}>
                <View style={style.center}>
                    <View style={style.avatarContainer}>
                        <Image style={style.avatarImage} />
                    </View>
                </View>
                <View style={style.center}>
                    <TouchableWithoutFeedback onPress={changeAvatar}>
                        <View style={style.changeAvatarContainer}>
                            <Text style={style.changeAvatarText}>Zmień Avatar</Text>
                        </View>
                    </TouchableWithoutFeedback>
                </View>
                <View style={style.center}>
                    <TouchableWithoutFeedback onPress={changeLogin}>
                        <View style={style.changeLoginContainer}>
                            <Text style={style.changeLoginText}>Zmień Login</Text>
                        </View>
                    </TouchableWithoutFeedback>
                </View>
                <View style={style.center}>
                    <TouchableWithoutFeedback onPress={changePassword}>
                        <View style={style.changePasswordContainer}>
                            <Text style={style.changePasswordText}>Zmień Hasło</Text>
                        </View>
                    </TouchableWithoutFeedback>
                </View>
                <View style={style.center}>
                    <TouchableWithoutFeedback onPress={logout}>
                        <View style={style.logoutContainer}>
                            <Text style={style.logoutText}>Wyloguj się</Text>
                        </View>
                    </TouchableWithoutFeedback>
                </View>
            </View>
            <View style={style.tabRow}>
                <TouchableWithoutFeedback onPress={()=>setList({
                    label: "Dołączone",
                    data: []
                })}>
                    <View style={(list.label == "Dołączone") ? style.tabActive : style.tabInactive}>
                        <Text style={style.text}>Dołączone mecze</Text>
                    </View>
                </TouchableWithoutFeedback>
                <TouchableWithoutFeedback onPress={()=>setList({
                    label: "Stworzone",
                    data: []
                })}>
                    <View style={(list.label == "Stworzone") ? style.tabActive : style.tabInactive}>
                        <Text style={style.text}>Stworzone mecze</Text>
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <ScrollView style={style.list}>
                {}
            </ScrollView>
            <BottomBar navigation={navigation} activeOption="Konto"/>
        </View>
    )
}

const style = StyleSheet.create({
    main: {
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
    userConfig: {
        width: "100%"
    },
    tabRow: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "space-around",
        marginTop: 10,
        marginBottom: 10
    },
    tabActive: {
        borderColor: "white",
        borderWidth: 1,
        borderRadius: 10,
        padding: 10,
        opacity: 1
    },
    tabInactive: {
        borderColor: "white",
        borderWidth: 1,
        borderRadius: 10,
        padding: 10,
        opacity: 0.4
    },
    list: {
        width: "100%"
    },
    text: {
        color: "white",
        fontSize: 20
    },
    invisible: {
        opacity: 0
    },
    center: {
        width: "100%",
        alignItems: "center"
    },
    avatarContainer: {},
    avatarImage: {},
    changeAvatarContainer: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        marginTop: 10,
        marginBottom: 10
    },
    changeAvatarText: {
        color: "white"
    },
    changeLoginContainer: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        marginTop: 10,
        marginBottom: 10
    },
    changeLoginText: {
        color: "white"
    },
    changePasswordContainer: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        marginTop: 10,
        marginBottom: 10
    },
    changePasswordText: {
        color: "white"
    },
    logoutContainer: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        marginTop: 10,
        marginBottom: 10
    },
    logoutText: {
        color: "white"
    }
});

export default Konto;