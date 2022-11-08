import { useEffect } from "react";
import { View, StyleSheet, TextInput, Text, TouchableWithoutFeedback, TouchableNativeFeedback } from "react-native";
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Login = ({navigation}) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const form = {
        login: "",
        password: ""
    }
    const loginChange = text=>{form.login = text}
    const passwordChange = text=>{form.password = text}
    const register = ()=>{
        navigation.navigate("Register");
    }
    const login = ()=>{
        fetch("http://10.0.2.2:8000/api/user/check", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify(form)
        })
        .then(response=>response.json())
        .then(data=>{
            if (data.status == "ok") {
                db.remove({}, { multi: true }, ()=>{
                    db.insert(form, ()=>{
                        navigation.navigate("Tablica");
                    });
                });
            } else {
                console.log("Login");
                console.log(data);
                // Tutaj będzie przypadek zwrócenia errora z bazy danych
            }
        }).catch(err=>{
            console.log(err);
            //  Tutaj będzie coś w przypadku problemów technicznych, z połączeniem
        });
    }

    return (
        <View style={style.main}>
            <Text style={style.header}>Zaloguj</Text>
            <TextInput style={style.loginForm} placeholder="Login" placeholderTextColor="white" onChangeText={loginChange}/>
            <TextInput style={style.passwordForm} placeholder="Hasło" placeholderTextColor="white" onChangeText={passwordChange}/>
            <TouchableWithoutFeedback onPress={login}>
                <View style={style.registerButtonContainer}>
                    <Text style={style.registerButtonText}>Zaloguj</Text>
                </View>
            </TouchableWithoutFeedback>
            <TouchableWithoutFeedback onPress={register}>
                <View style={style.getAccountNotificationContainer}>
                    <Text style={style.getAccountNotificationText}>Zarejestruj się</Text>
                </View>
            </TouchableWithoutFeedback>
        </View>
    )
}

const style = StyleSheet.create({
    main: {
        width: "100%",
        height: "100%",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        backgroundColor: "#1c1c1c"
    },
    header: {
        fontSize: 40,
        color: "white"
    },
    loginForm: {
        width: "80%",
        color: "white" ,
        padding: 10,
        margin: 15,
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 10
    },
    passwordForm: {
        width: "80%",
        color: "white" ,
        padding: 10,
        margin: 15,
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 10
    },
    registerButtonContainer: {
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        color: "white" ,
        padding: 10,
        margin: 15,
        borderWidth: 2,
        borderColor: "white",
        borderRadius: 10
    },
    registerButtonText: {
        color: "white",
        fontSize: 25,
        fontWeight: "bold"
    },
    getAccountNotificationContainer: {
        marginTop: 75
    },
    getAccountNotificationText: {
        color: "white",
        fontSize: 17,
        textDecorationLine: "underline"
    }
});

export default Login