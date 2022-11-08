import { Text, TextInput, View, StyleSheet, TouchableWithoutFeedback, ScrollView } from 'react-native';
import BottomBar from '../components/BottomBar';
import ReturnArrowIco from '../svg/ReturnArrowIco';

const Search = ({navigation})=>{
    return (
        <View style={style.main}>
            <View style={style.topBar}>
                <TouchableWithoutFeedback onPress={()=>{navigation.navigate("Tablica")}}>
                    <View>
                        <ReturnArrowIco />
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <ScrollView style={style.scrollView}>
                <Text style={style.text}>Wyszukaj</Text>
                <View style={{alignItems: "center"}}>
                    <TextInput style={style.searchBar} />
                </View>
            </ScrollView>
            <BottomBar navigation={navigation} />
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
        paddingTop: 10,
        paddingLeft: 10,
        alignItems: "flex-start"
    },
    scrollView: {
        width: "100%",
        textAlign: "center"
    },
    text: {
        color: "white",
        textAlign: "center",
        fontSize: 25,
        marginBottom: 15,
        marginTop: 15
    },
    searchBar: {
        color: "white",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 10,
        padding: 5,
        width: "85%"
    }
});

export default Search;