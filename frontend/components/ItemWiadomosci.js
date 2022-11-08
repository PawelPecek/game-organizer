import { Image, View, Text, StyleSheet, TouchableWithoutFeedback } from 'react-native';
import Avatar from '../img/avatar.png'

const ItemWiadomosci = ({ avatar, name }) => {
  return (
    <TouchableWithoutFeedback>
      <View style={style.main}>
        <View>
          <Image style={style.img} source={Avatar} />
        </View>
        <View>
          <Text style={style.text}>{name}</Text>
        </View>
      </View>
    </TouchableWithoutFeedback>
  )
}

const style = StyleSheet.create({
    main: {
      width: "100%",
      flexDirection: "row",
      borderWidth: 2,
      borderColor: "white",
      borderRadius: 10,
      marginBottom: 10
    },
    img: {
      width: 200,
      height: 200,
      borderRadius: 200
    },
    text: {
      color: "white",
      fontSize: 25,
      paddingLeft: 20,
      paddingTop: 50
    }
});

export default ItemWiadomosci
